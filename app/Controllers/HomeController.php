<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Location;
use App\Models\Trip;

final class HomeController extends Controller
{
    public function index(): void
    {
        $location = new Location();
        $trip = new Trip();
        $locations = $location->all();
        $featuredTrips = $trip->search([]);
        if (!empty($featuredTrips)) {
            shuffle($featuredTrips);
            $featuredTrips = array_slice($featuredTrips, 0, 3);
        }

        $featuredNews = [
            [
                'id' => 1,
                'title' => 'LobiBus chính thức khai trương 10 tuyến xe giường nằm mới kết nối miền Tây',
                'summary' => 'Mở rộng mạng lưới phục vụ hành khách TP. Hồ Chí Minh đi các tỉnh miền Tây với dịch vụ chất lượng cao và giá vé ưu đãi.',
                'image' => 'images/news/mientay_bus.png',
                'date' => '20-05-2026',
            ],
            [
                'id' => 2,
                'title' => 'Trải nghiệm dòng xe Limousine giường phòng siêu VIP thế hệ mới',
                'summary' => 'Ra mắt dòng xe Limousine riêng tư, sang trọng và tiện nghi cho các tuyến hot liên tỉnh.',
                'image' => 'images/news/limousine_vip.png',
                'date' => '18-05-2026',
            ],
            [
                'id' => 3,
                'title' => 'LobiBus vinh dự nhận giải thưởng "Hãng xe khách được yêu thích nhất năm 2025"',
                'summary' => 'Ghi nhận nỗ lực nâng cao chất lượng dịch vụ và độ hài lòng của khách hàng trên toàn hệ thống.',
                'image' => 'images/news/award_2025.png',
                'date' => '15-05-2026',
            ],
        ];

        $this->view('home.index', [
            'title' => 'Trang chủ',
            'locations' => $locations,
            'featuredTrips' => $featuredTrips,
            'featuredNews' => $featuredNews,
        ]);
    }
}
