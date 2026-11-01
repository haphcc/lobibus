<?php
$pageCss = ['chatbot.css'];
$pageJs = ['chatbot.js'];
?>
<section class="chatbot-page">
    <div class="container">
        <div class="chatbot-header">
            <div>
                <span class="section-kicker" style="color: #0f766e; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 6px;">Trợ lý ảo LobiBus AI</span>
                <h1>Trợ lý LobiBus</h1>
                <p>Trợ lý ảo LobiBus được tích hợp trực tiếp với Cơ sở dữ liệu của bài. Bạn có thể tra cứu chuyến xe, giá vé, kiểm tra trạng thái vé đặt, xem đánh giá của khách hàng trong thời gian thực bằng ngôn ngữ tự nhiên.</p>
            </div>
            <div class="chatbot-service-card">
                <span>Trạng thái kết nối</span>
                <strong>
                    <span class="ai-status-pulse"></span> Đang hoạt động
                </strong>
                <small>Đồng bộ trực tiếp CSDL</small>
            </div>
        </div>

        <div class="chatbot-layout">
            <aside class="chatbot-sidebar">
                <div class="assistant-card">
                    <div class="assistant-avatar">AI</div>
                    <div>
                        <strong>LobiBus Smart Assistant</strong>
                        <span>Động cơ Hybrid AI & CSDL 24/7</span>
                    </div>
                </div>



                <div class="chatbot-sidebar-section">
                    <h2>Câu hỏi truy vấn thực tế</h2>
                    <div class="quick-question-grid">
                        <button type="button" class="quick-question" data-question="Tuyến Hà Nội đi Hải Phòng có những chuyến nào?">
                            <strong>Tra cứu Chuyến xe</strong>
                            <span>"Tuyến Hà Nội đi Hải Phòng..."</span>
                        </button>
                        <button type="button" class="quick-question" data-question="Tra cứu mã đặt vé LB-20260520-0001">
                            <strong>Kiểm tra Đơn hàng</strong>
                            <span>"Tra cứu vé LB-20260520-0001"</span>
                        </button>
                        <button type="button" class="quick-question" data-question="Kiểm tra các vé đã đặt của số điện thoại 0911000001">
                            <strong>Tra cứu theo SĐT</strong>
                            <span>"Tìm vé của SĐT 0911000001"</span>
                        </button>
                        <button type="button" class="quick-question" data-question="Xem các nhận xét và đánh giá gần đây của khách hàng">
                            <strong>Xem Đánh giá / Review</strong>
                            <span>"Đánh giá gần đây của khách..."</span>
                        </button>
                        <button type="button" class="quick-question" data-question="Hệ thống đang chạy những tuyến đường nào?">
                            <strong>Danh sách Tuyến đường</strong>
                            <span>"Các tuyến đường đang chạy..."</span>
                        </button>
                    </div>
                </div>

                <div class="chatbot-note">
                    <strong>💡 Mẹo kiểm tra CSDL:</strong>
                    <p>Trợ lý ảo LobiBus có thể tự động ánh xạ ngôn ngữ của bạn sang các truy vấn CSDL cực kỳ chính xác. Hãy nhập mã vé hoặc tuyến đường thực tế để kiểm tra!</p>
                </div>
            </aside>

            <div class="chat-window">
                <div class="chat-window-header">
                    <div>
                        <strong>Cuộc trò chuyện trợ lý</strong>
                        <span>Hệ thống phân tích Intent & Tool Calling thời gian thực</span>
                    </div>
                    <span class="chat-status-dot">Online</span>
                </div>

                <div id="chatMessages" class="chat-messages" aria-live="polite">
                    <div class="chat-message bot">
                        <span>LobiBus</span>
                        <p>Xin chào! Mình là Trợ lý ảo thông minh LobiBus. Mình đã kết nối trực tiếp với CSDL hệ thống.</p>
                        <p>Bạn có thể hỏi mình bất kỳ điều gì liên quan đến chuyến xe, đặt vé, trạng thái thanh toán, đánh giá dịch vụ hoặc các chính sách khác nhé!</p>
                    </div>
                </div>

                <form id="chatbotForm" class="chat-composer">
                    <input id="chatbotMessage" class="form-control" placeholder="Nhập câu hỏi của bạn (ví dụ: Tuyến Hà Nội đi Hải Phòng)..." autocomplete="off">
                    <button class="btn btn-success" type="submit">Gửi</button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
/* Hiệu ứng LED xanh nhấp nháy cho Trạng thái AI */
.ai-status-pulse {
    display: inline-block;
    width: 10px;
    height: 10px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse 1.6s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}
</style>
