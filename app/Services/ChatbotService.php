<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Chatbot;

final class ChatbotService
{
    public function reply(string $message): string
    {
        if (trim($message) === '') {
            return 'Vui lòng nhập câu hỏi để LobiBus có thể hỗ trợ bạn.';
        }

        return (new Chatbot())->findAnswer($message)
            ?? 'Cảm ơn bạn đã liên hệ LobiBus. Hiện tại mình chưa có câu trả lời phù hợp, bạn vui lòng thử hỏi về đặt vé, hủy vé, thanh toán hoặc tra cứu vé.';
    }
}
