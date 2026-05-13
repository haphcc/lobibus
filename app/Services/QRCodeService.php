<?php
declare(strict_types=1);
namespace App\Services;

final class QRCodeService
{
    public function generate(string $content): string
    {
        // TODO: install endroid/qr-code or chillerlan/php-qrcode and write image to public/assets/qrcodes.
        return '/assets/images/Micro_QR_Example.svg';
    }
}
