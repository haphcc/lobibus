<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Chatbot extends Model
{
    public function findAnswer(string $question): ?string
    {
        return null;
    }
}
