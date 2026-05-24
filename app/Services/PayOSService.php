<?php

declare(strict_types=1);

namespace App\Services;

final class PayOSService
{
    private string $baseUrl;
    private string $clientId;
    private string $apiKey;
    private string $checksumKey;

    public function __construct()
    {
        $config = \config('payos');
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://api-merchant.payos.vn'), '/');
        $this->clientId = trim((string) ($config['client_id'] ?? ''));
        $this->apiKey = trim((string) ($config['api_key'] ?? ''));
        $this->checksumKey = trim((string) ($config['checksum_key'] ?? ''));
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->apiKey !== '' && $this->checksumKey !== '';
    }

    public function orderCodeForBooking(int $bookingId): int
    {
        return 100000 + $bookingId;
    }

    public function createPaymentLink(array $booking, array $payment, string $returnUrl, string $cancelUrl): array
    {
        $this->assertConfigured();

        $orderCode = $this->orderCodeForBooking((int) ($booking['id'] ?? 0));
        $amount = (int) round((float) ($payment['amount'] ?? $booking['total_amount'] ?? 0));
        $description = $this->description((string) ($booking['booking_code'] ?? ''), $orderCode);

        $payload = [
            'orderCode' => $orderCode,
            'amount' => $amount,
            'description' => $description,
            'buyerName' => (string) ($booking['customer_name'] ?? ''),
            'buyerEmail' => (string) ($booking['customer_email'] ?? ''),
            'buyerPhone' => (string) ($booking['customer_phone'] ?? ''),
            'items' => [[
                'name' => 'Ve xe LobiBus',
                'quantity' => max(1, count($booking['seats'] ?? [])),
                'price' => $amount,
            ]],
            'cancelUrl' => $cancelUrl,
            'returnUrl' => $returnUrl,
        ];
        $payload['signature'] = $this->signCreatePayload($payload);

        try {
            return $this->request('POST', '/v2/payment-requests', $payload)['data'] ?? [];
        } catch (\RuntimeException $exception) {
            $existing = $this->getPaymentRequest($orderCode);
            if ($existing !== []) {
                return $existing;
            }

            throw $exception;
        }
    }

    public function getPaymentRequest(int|string $id): array
    {
        $this->assertConfigured();

        try {
            return $this->request('GET', '/v2/payment-requests/' . rawurlencode((string) $id))['data'] ?? [];
        } catch (\RuntimeException) {
            return [];
        }
    }

    private function signCreatePayload(array $payload): string
    {
        $data = 'amount=' . $payload['amount']
            . '&cancelUrl=' . $payload['cancelUrl']
            . '&description=' . $payload['description']
            . '&orderCode=' . $payload['orderCode']
            . '&returnUrl=' . $payload['returnUrl'];

        return hash_hmac('sha256', $data, $this->checksumKey);
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $ch = curl_init($this->baseUrl . $path);
        if ($ch === false) {
            throw new \RuntimeException('Khong the khoi tao ket noi payOS.');
        }

        $headers = [
            'Content-Type: application/json',
            'x-client-id: ' . $this->clientId,
            'x-api-key: ' . $this->apiKey,
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 20,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($raw === false || $error !== '') {
            throw new \RuntimeException('Khong the ket noi payOS: ' . $error);
        }

        $response = json_decode((string) $raw, true);
        if (!is_array($response)) {
            throw new \RuntimeException('Phan hoi payOS khong hop le.');
        }

        if ($status >= 400 || (string) ($response['code'] ?? '') !== '00') {
            throw new \RuntimeException((string) ($response['desc'] ?? 'payOS tu choi yeu cau thanh toan.'));
        }

        return $response;
    }

    private function assertConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('Chua cau hinh PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY.');
        }
    }

    private function description(string $bookingCode, int $orderCode): string
    {
        $description = 'LB' . $orderCode;
        return substr($description, 0, 9);
    }
}
