<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class NewsController extends Controller
{
    private static function getArticles(): array
    {
        return [
            [
                'id' => 1,
                'category' => 'featured',
                'title' => 'LobiBus chính thức khai trương 10 tuyến xe giường nằm mới kết nối miền Tây',
                'summary' => 'Nhằm đáp ứng nhu cầu đi lại ngày càng tăng cao của người dân, LobiBus chính thức đưa vào khai thác 10 tuyến xe giường nằm chất lượng cao kết nối trực tiếp TP. Hồ Chí Minh với các tỉnh miền Tây Nam Bộ kể từ tháng 5/2026.',
                'image' => 'images/news/mientay_bus.png',
                'date' => '20-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Nhằm nâng cao chất lượng dịch vụ và mở rộng mạng lưới đường bay mặt đất, LobiBus tự hào thông báo chính thức khai trương 10 tuyến xe giường nằm chất lượng cao mới. Các tuyến xe mới sẽ kết nối trực tiếp TP. Hồ Chí Minh (Bến xe Miền Tây) đi các tỉnh trọng điểm như Cần Thơ, An Giang, Kiên Giang, Cà Mau, Sóc Trăng, Bạc Liêu, Đồng Tháp, Trà Vinh, Vĩnh Long và Bến Tre.<br/><br/>
Các dòng xe phục vụ trên các tuyến này đều là phiên bản xe giường nằm thế hệ mới nhất của LobiBus, trang bị đầy đủ các tiện ích cao cấp như hệ thống giảm xóc khí nén êm ái, wifi tốc độ cao miễn phí, cổng sạc USB tại mỗi giường, và nước uống, khăn lạnh miễn phí suốt hành trình.<br/><br/>
Đại diện LobiBus chia sẻ: "Việc mở rộng thêm 10 tuyến kết nối miền Tây là một phần trong chiến lược dài hạn nhằm nâng cao trải nghiệm khách hàng và tối ưu hóa thời gian di chuyển của hành khách. Chúng tôi cam kết duy trì tần suất chạy xe dày đặc, giá vé hợp lý và phong cách phục vụ chuyên nghiệp từ đội ngũ bác tài, nhân viên phục vụ."<br/><br/>
Nhân dịp khai trương, LobiBus áp dụng chương trình khuyến mãi đặc biệt: <b>Giảm ngay 20% giá vé</b> cho tất cả hành khách đặt vé trực tuyến thông qua website hoặc ứng dụng di động LobiBus từ nay đến hết ngày 31/05/2026. Hãy nhanh tay đặt vé ngay hôm nay để trải nghiệm dịch vụ vận tải hàng đầu Việt Nam!'
            ],
            [
                'id' => 2,
                'category' => 'featured',
                'title' => 'Trải nghiệm dòng xe Limousine giường phòng siêu VIP thế hệ mới của LobiBus',
                'summary' => 'Với mong muốn đem lại trải nghiệm hoàng gia trên mỗi cung đường, LobiBus ra mắt dòng chuyên cơ mặt đất Limousine 20 phòng nằm đẳng cấp với không gian hoàn toàn riêng tư và sang trọng bậc nhất.',
                'image' => 'images/news/limousine_vip.png',
                'date' => '18-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Được mệnh danh là "chuyên cơ mặt đất", dòng xe Limousine phòng nằm VIP mới của LobiBus sở hữu thiết kế đột phá với 20 khoang phòng riêng biệt, đem lại sự riêng tư tuyệt đối cho hành khách suốt hành trình dài.<br/><br/>
Mỗi phòng nằm được thiết kế như một cabin thu nhỏ, trang bị ghế da cao cấp tích hợp tính năng massage đa điểm, màn hình giải trí LCD chuẩn HD riêng biệt với hàng trăm bộ phim và chương trình ca nhạc cập nhật liên tục, tai nghe chống ồn, và cổng sạc nhanh đa năng.<br/><br/>
Không chỉ dừng lại ở trang thiết bị phần cứng, LobiBus còn đặc biệt chú trọng đến sự an toàn của hành khách khi trang bị hệ thống phanh ABS thế hệ mới, camera giám sát hành trình cùng hệ thống cảnh báo chệch làn đường ADAS tiên tiến nhất. Đội ngũ tài xế phục vụ dòng xe Limousine này đều là những bác tài có kinh nghiệm lâu năm và trải qua các khóa huấn luyện dịch vụ khách hàng tiêu chuẩn 5 sao.<br/><br/>
Hiện tại, dòng xe Limousine VIP này đang được đưa vào khai thác trên các tuyến hot như TP. Hồ Chí Minh - Đà Lạt, TP. Hồ Chí Minh - Nha Trang và Hà Nội - Sa Pa. Hãy đặt vé ngay để tận hưởng hành trình êm ái, thư thái và tiện nghi như ở nhà cùng LobiBus!'
            ],
            [
                'id' => 3,
                'category' => 'spotlight',
                'title' => 'LobiBus vinh dự nhận giải thưởng "Hãng xe khách được yêu thích nhất năm 2025"',
                'summary' => 'Tại lễ trao giải Vận tải Việt Nam diễn ra tại Hà Nội, LobiBus đã xuất sắc vượt qua nhiều ứng cử viên để nhận danh hiệu hãng xe khách uy tín và được khách hàng bình chọn nhiều nhất.',
                'image' => 'images/news/award_2025.png',
                'date' => '15-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Lễ trao giải thưởng uy tín ngành Giao thông Vận tải Việt Nam 2025 vừa diễn ra trang trọng tại Trung tâm Hội nghị Quốc gia, Hà Nội. LobiBus đã vinh dự được xướng tên tại hạng mục danh giá nhất: "Hãng xe khách được yêu thích nhất năm 2025".<br/><br/>
Giải thưởng là kết quả của cuộc khảo sát quy mô toàn quốc với hơn 500,000 lượt bình chọn từ người tiêu dùng, kết hợp với sự đánh giá khắt khe của hội đồng chuyên môn về các tiêu chí: tỷ lệ xuất bến đúng giờ, mức độ an toàn giao thông, chất lượng phương tiện và dịch vụ chăm sóc khách hàng.<br/><br/>
Trong năm 2025, LobiBus đã phục vụ hơn 5 triệu lượt khách an toàn, triển khai hàng loạt cải tiến công nghệ bao gồm hệ thống đặt vé thông minh AI, nâng cấp ứng dụng di động LobiBus và ra mắt trợ lý ảo hỗ trợ 24/7. Giải thưởng này là minh chứng rõ nét cho sự nỗ lực không ngừng nghỉ của toàn bộ tập thể cán bộ công nhân viên LobiBus.<br/><br/>
Ông Nguyễn Văn A, Tổng Giám đốc LobiBus phát biểu tại buổi lễ: "Chúng tôi vô cùng tự hào và biết ơn sự tin tưởng, đồng hành của hàng triệu khách hàng Việt Nam. Danh hiệu này vừa là niềm vinh dự to lớn, vừa là trách nhiệm nhắc nhở chúng tôi phải tiếp tục nâng cao tiêu chuẩn dịch vụ, mang lại những giá trị tốt nhất cho cộng đồng."'
            ],
            [
                'id' => 4,
                'category' => 'spotlight',
                'title' => 'Chiến dịch "Chuyến xe xanh - Vạn dặm an lành" LobiBus chung tay vì môi trường',
                'summary' => 'Chương trình cắt giảm khí thải CO2 và hạn chế rác thải nhựa của LobiBus đã chính thức bước sang năm thứ 3, mang lại nhiều kết quả tích cực cho cộng đồng xanh.',
                'image' => 'images/news/green_bus.png',
                'date' => '12-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Là doanh nghiệp vận tải tiên phong trong các hoạt động xã hội và bảo vệ môi trường, LobiBus tiếp tục đẩy mạnh chiến dịch "Chuyến xe xanh - Vạn dặm an lành" trên quy mô toàn quốc.<br/><br/>
Mục tiêu trọng tâm của chiến dịch trong năm 2026 là giảm thiểu 15% lượng khí thải CO2 thông qua việc bảo dưỡng định kỳ nghiêm ngặt và áp dụng công nghệ lái xe sinh thái tiết kiệm nhiên liệu cho toàn bộ đội ngũ tài xế. Đồng thời, LobiBus cam kết 100% không sử dụng chai nhựa dùng một lần trên các tuyến xe chất lượng cao, thay vào đó là sử dụng ly giấy tự phân hủy sinh học và khuyến khích hành khách mang theo bình nước cá nhân.<br/><br/>
Hành khách đi xe cũng hào hứng tham gia hoạt động "Tích điểm xanh - Đổi vé xe" trên ứng dụng di động LobiBus, nơi mỗi hành trình xanh sẽ được quy đổi thành điểm thưởng để nhận mã giảm giá vé hoặc đóng góp vào quỹ trồng rừng quốc gia.<br/><br/>
Chiến dịch không chỉ giúp giảm tác động xấu lên môi trường mà còn lan tỏa mạnh mẽ thông điệp sống xanh, có trách nhiệm đến hàng triệu hành khách. LobiBus hy vọng sẽ tiếp tục nhận được sự đồng hành từ quý khách hàng để cùng nhau xây dựng những hành trình xanh vững bền.'
            ],
            [
                'id' => 5,
                'category' => 'all',
                'title' => 'Thông báo chính thức mở bán vé Tết Bính Ngọ 2026 của LobiBus',
                'summary' => 'Để bảo đảm quyền lợi và sự thuận tiện tối đa cho người dân về quê đón Tết, LobiBus công bố kế hoạch chi tiết về thời gian mở bán vé và lịch trình tăng cường phục vụ Tết 2026.',
                'image' => 'images/news/tet_2026.png',
                'date' => '10-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Công ty Cổ phần Xe khách LobiBus chính thức công bố lịch mở bán vé xe Tết Bính Ngọ 2026 bắt đầu từ ngày 15/05/2026. Lịch trình phục vụ cao điểm Tết sẽ diễn ra từ ngày 20 tháng Chạp đến hết mùng 10 tháng Giêng.<br/><br/>
Để tránh tình trạng quá tải và đầu cơ vé, LobiBus sẽ triển khai bán vé đồng bộ qua hai kênh: trực tuyến trên website/app LobiBus và trực tiếp tại các phòng vé chính thức của hãng ở các bến xe lớn. Khách hàng đặt vé trực tuyến sẽ được lựa chọn chính xác sơ đồ giường nằm và nhận vé điện tử kèm mã QR qua email và SMS.<br/><br/>
Nhằm phục vụ tốt nhất nhu cầu của hành khách, LobiBus dự kiến tăng cường thêm 30% tần suất chạy xe trên tất cả các tuyến trọng điểm từ TP. Hồ Chí Minh đi miền Trung, Tây Nguyên và miền Tây. Hãng cam kết: <b>Giữ nguyên giá vé niêm yết theo quy định của Sở Giao thông Vận tải</b>, không tự ý phụ thu hoặc tăng giá vé trái phép dưới mọi hình thức.<br/><br/>
Hãy lên kế hoạch sớm và đặt vé qua các kênh chính thống của LobiBus để bảo đảm có một chuyến đi an toàn, ấm áp đoàn viên cùng gia đình!'
            ],
            [
                'id' => 6,
                'category' => 'all',
                'title' => 'Hướng dẫn đặt vé xe LobiBus online cực kỳ nhanh chóng và an toàn trong 2 phút',
                'summary' => 'Chỉ với vài thao tác đơn giản trên điện thoại hoặc máy tính, bạn đã có thể sở hữu ngay tấm vé xe LobiBus chất lượng cao cho chuyến đi của mình.',
                'image' => 'images/news/booking_online.png',
                'date' => '08-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Thời đại công nghệ số mang đến sự tiện lợi vượt trội cho người tiêu dùng. Giờ đây, thay vì phải ra tận bến xe xếp hàng mua vé, bạn hoàn toàn có thể đặt vé xe LobiBus chỉ trong vòng 2 phút bằng ứng dụng di động hoặc website chính thức.<br/><br/>
<b>Các bước đặt vé cực kỳ đơn giản:</b><br/>
1. <b>Tìm kiếm chuyến đi:</b> Truy cập website <a href="/">LobiBus</a> hoặc ứng dụng LobiBus, nhập Điểm đi, Điểm đến và Ngày đi mong muốn.<br/>
2. <b>Chọn chuyến xe:</b> Hệ thống sẽ hiển thị toàn bộ danh sách chuyến xe với thông tin chi tiết về giờ xuất bến, loại xe (giường nằm, limousine, ghế ngồi) và giá vé rõ ràng.<br/>
3. <b>Chọn vị trí ngồi:</b> Sơ đồ xe trực quan giúp bạn dễ dàng chọn chính xác giường/ghế trống ưa thích của mình.<br/>
4. <b>Nhập thông tin & Thanh toán:</b> Điền đầy đủ thông tin hành khách và tiến hành thanh toán qua các cổng thanh toán an toàn như ví điện tử (Momo, VNPay), thẻ ATM nội địa hoặc thẻ tín dụng quốc tế.<br/><br/>
Sau khi thanh toán thành công, hệ thống LobiBus sẽ gửi tin nhắn SMS và email chứa Vé điện tử (Mã QR) đến số điện thoại và email đăng ký của bạn. Khi lên xe, bạn chỉ cần xuất trình mã QR này cho nhân viên soát vé quét mã là có thể bắt đầu hành trình. Thật tiện lợi và nhanh chóng!'
            ],
            [
                'id' => 7,
                'category' => 'all',
                'title' => 'LobiBus nâng cấp hệ thống thanh toán điện tử thông minh bảo mật tối đa',
                'summary' => 'Hợp tác cùng các tổ chức tài chính lớn, LobiBus tối ưu hóa quy trình thanh toán không tiền mặt với công nghệ mã hóa thẻ và xác thực sinh trắc học tiên tiến.',
                'image' => 'images/news/secure_payment.png',
                'date' => '05-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Nhằm mang đến trải nghiệm giao dịch an toàn và nhanh chóng nhất cho khách hàng khi mua vé trực tuyến, LobiBus vừa hoàn tất đợt nâng cấp toàn diện hạ tầng thanh toán điện tử trên cả hai nền tảng Website và Mobile App.<br/><br/>
Trong đợt nâng cấp này, LobiBus đã tích hợp chuẩn bảo mật quốc tế PCI DSS cấp độ cao nhất dành cho giao dịch thẻ. Mọi thông tin thẻ tín dụng của khách hàng đều được mã hóa (tokenization) và không lưu trữ trên máy chủ LobiBus, loại bỏ hoàn toàn nguy cơ rò rỉ dữ liệu.<br/><br/>
Đồng thời, hệ thống cũng bổ sung tính năng thanh toán nhanh qua quét mã VNPAY-QR thế hệ mới, hỗ trợ Apple Pay và Google Pay giúp hoàn thành giao dịch chỉ với một chạm bằng nhận diện vân tay hoặc khuôn mặt (sinh trắc học).<br/><br/>
Sự cải tiến này không chỉ giúp nâng cao tính bảo mật mà còn giảm thiểu tối đa thời gian chờ đợi xử lý thanh toán xuống dưới 3 giây, đem đến sự yên tâm tuyệt đối cho khách hàng mỗi khi giao dịch đặt vé cùng LobiBus.'
            ],
            [
                'id' => 8,
                'category' => 'all',
                'title' => 'LobiBus triển khai đào tạo kỹ năng sơ cấp cứu chuyên sâu cho toàn bộ lái xe',
                'summary' => 'Đảm bảo an toàn sức khỏe tối đa cho hành khách, LobiBus phối hợp cùng Hội Chữ thập đỏ tổ chức khóa đào tạo kỹ năng sơ cấp cứu và xử lý tình huống y tế khẩn cấp chuyên sâu.',
                'image' => 'images/news/first_aid.png',
                'date' => '03-05-2026',
                'author' => 'Ban An toàn LobiBus',
                'content' => 'An toàn trên mỗi chuyến đi không chỉ dừng lại ở kỹ năng vận hành xe an toàn mà còn ở khả năng ứng phó linh hoạt trước các tình huống sức khỏe của hành khách. Ý thức rõ điều đó, LobiBus đã chính thức triển khai chương trình đào tạo chuyên sâu về kỹ năng sơ cấp cứu cho toàn thể đội ngũ lái xe, phụ xe và nhân viên phục vụ tại nhà ga.<br/><br/>
Chương trình được phối hợp thiết kế và trực tiếp giảng dạy bởi các chuyên gia y tế giàu kinh nghiệm đến từ Hội Chữ thập đỏ. Các học viên được thực hành trực quan các kỹ năng thiết yếu bao gồm: hà hơi thổi ngạt, hồi sức tim phổi (CPR), băng bó vết thương hở, cố định xương gãy, xử lý hóc dị vật đường thở và chăm sóc hành khách bị say nắng, hạ đường huyết hoặc gặp chấn thương nhẹ.<br/><br/>
Kết thúc khóa huấn luyện, 100% học viên tham gia phải vượt qua bài kiểm tra thực hành nghiêm ngặt để được cấp chứng chỉ hoàn thành khóa học. Đồng thời, LobiBus đã nâng cấp toàn bộ tủ thuốc y tế trên xe với đầy đủ các trang thiết bị sơ cứu đạt chuẩn Bộ Y tế.<br/><br/>
Sự đầu tư bài bản này giúp đội ngũ nhân viên LobiBus luôn tự tin, sẵn sàng hỗ trợ y tế kịp thời cho hành khách, đem lại sự an tâm tuyệt đối trên mỗi hành trình cùng chúng tôi.'
            ],
            [
                'id' => 9,
                'category' => 'all',
                'title' => 'Đặt vé xe LobiBus qua ví điện tử VNPay nhận ngàn ưu đãi hấp dẫn',
                'summary' => 'Sự kết hợp hoàn hảo giữa LobiBus và VNPay mang lại giải pháp thanh toán không chạm cực nhanh cùng cơ hội hoàn tiền, giảm giá cực khủng cho hành khách trong mùa hè này.',
                'image' => 'images/news/vnpay_promo.png',
                'date' => '01-05-2026',
                'author' => 'Ban Truyền thông LobiBus',
                'content' => 'Tiếp tục hành trình số hóa dịch vụ vận tải khách hàng, LobiBus hợp tác chiến lược cùng Cổng thanh toán quốc gia VNPay mang lại trải nghiệm mua vé và thanh toán trực tuyến vô cùng đơn giản, nhanh chóng.<br/><br/>
Giờ đây, ngay trên ứng dụng ngân hàng di động (Mobile Banking) hoặc ví điện tử VNPay của bạn, việc tìm kiếm tuyến đường, chọn giờ đi và đặt vé xe LobiBus được thực hiện chỉ với vài thao tác vuốt chạm. Bạn không cần phải nhập thông tin thẻ hay tài khoản phức tạp, chỉ cần xác nhận thanh toán bằng mã PIN hoặc sinh trắc học (vân tay, khuôn mặt) là hoàn tất.<br/><br/>
Nhân dịp ra mắt tính năng đặt vé đồng bộ, từ ngày 01/05 đến hết ngày 30/06/2026, LobiBus và VNPay tung ra chương trình khuyến mại khổng lồ: <b>Nhập mã "LOBIBUS99" giảm ngay 30,000đ</b> cho mọi giao dịch thanh toán vé xe có giá trị từ 150,000đ trở lên. Ngoài ra, khách hàng may mắn sẽ có cơ hội được hoàn tiền lên tới 50% giá trị vé ở các khung giờ vàng.<br/><br/>
Tiện lợi tối đa, bảo mật tuyệt đối lại cực kỳ tiết kiệm, còn chần chừ gì nữa mà không mở ngay ví điện tử VNPay để đặt vé LobiBus cho những chuyến du lịch mùa hè đầy sôi động sắp tới!'
            ]
        ];
    }

    public function index(): void
    {
        $articles = self::getArticles();

        // Categorize articles
        $featured = array_filter($articles, fn($a) => $a['category'] === 'featured');
        $spotlight = array_filter($articles, fn($a) => $a['category'] === 'spotlight');
        $all = $articles; // All news includes all articles chronologically

        $this->view('news.index', [
            'title' => 'Tin tức - LobiBus',
            'featured' => $featured,
            'spotlight' => $spotlight,
            'all' => $all
        ]);
    }

    public function detail(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $articles = self::getArticles();

        $article = null;
        foreach ($articles as $a) {
            if ($a['id'] === $id) {
                $article = $a;
                break;
            }
        }

        if ($article === null) {
            header('Location: ' . \url('/news'));
            exit;
        }

        // Get related articles (exclude current, take 3 items)
        $related = array_filter($articles, fn($a) => $a['id'] !== $id);
        $related = array_slice($related, 0, 3);

        $this->view('news.detail', [
            'title' => $article['title'] . ' - LobiBus',
            'article' => $article,
            'related' => $related
        ]);
    }
}
