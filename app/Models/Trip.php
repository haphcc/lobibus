<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\Model;

final class Trip extends Model
{
    public function search(array $filters = []): array
    {
        return [
            [
                'id' => 1,
                'from' => $filters['from'] ?? 'Hà Nội',
                'to' => $filters['to'] ?? 'Hải Phòng',
                'bus_name' => 'LobiBus Express',
                'departure_time' => '2026-05-20 08:00:00',
                'arrival_time' => '2026-05-20 10:30:00',
                'price' => 150000,
                'available_seats' => 28,
            ],
        ];
    }

    public function find(int $id): ?array { return $this->search()[0] ?? null; }
    public function getAvailableSeats(int $id): int { return 28; }
}
