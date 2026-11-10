<?php
declare(strict_types=1);
namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

final class QRCodeService
{
    public function generate(string $content, string $ticketCode = ''): string
    {
        $directory = dirname(__DIR__, 2) . '/public/assets/qrcodes';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $safeCode = preg_replace('/[^A-Za-z0-9_-]/', '-', $ticketCode !== '' ? $ticketCode : sha1($content));
        $fileName = $safeCode . '.svg';
        $absolutePath = $directory . '/' . $fileName;

        $options = new QROptions;
        $options->outputBase64 = false;
        $options->svgAddXmlHeader = true;
        $options->svgViewBoxSize = 300;
        $options->drawLightModules = true;
        $options->connectPaths = true;

        $svg = (new QRCode($options))->render($content);
        if (file_put_contents($absolutePath, $svg) === false) {
            throw new \RuntimeException('Khong the ghi file QR code.');
        }

        return 'qrcodes/' . $fileName;
    }
}
