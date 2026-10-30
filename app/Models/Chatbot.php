<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Chatbot extends Model
{
    public function findAnswer(string $question): ?string
    {
        $message = $this->normalizeText($question);
        if ($message === '') {
            return null;
        }

        $statement = $this->db()->query('SELECT keyword, question, answer FROM chatbot_questions ORDER BY id ASC');
        $rows = $statement->fetchAll();

        foreach ($rows as $row) {
            $keyword = $this->normalizeText((string) $row['keyword']);
            $storedQuestion = $this->normalizeText((string) $row['question']);

            if ($keyword !== '' && str_contains($message, $keyword)) {
                return (string) $row['answer'];
            }

            if ($storedQuestion !== '' && str_contains($storedQuestion, $message)) {
                return (string) $row['answer'];
            }
        }

        return null;
    }

    private function normalizeText(string $text): string
    {
        $text = trim(mb_strtolower($text));
        $accented = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
        ];
        $plain = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
        ];

        return str_replace($accented, $plain, $text);
    }
}
