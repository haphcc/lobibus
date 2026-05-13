<?php
declare(strict_types=1);
namespace App\Services;

final class ChatbotService
{
    public function reply(string $message): string
    {
        $text = mb_strtolower($message);
        if (str_contains($text, 'hủy') || str_contains($text, 'doi ve')) {
            return 'Bạn có thể hủy vé trong mục Lịch sử đặt vé nếu vé còn đủ điều kiện.';
        }

        if (str_contains($text, 'thanh toán')) {
            return 'LobiBus dự kiến hỗ trợ tiền mặt, ví điện tử và chuyển khoản. TODO: nối PaymentService.';
        }

        return 'Cảm ơn bạn đã liên hệ LobiBus. TODO: bổ sung dữ liệu chatbot_questions trong database.';
    }
}
