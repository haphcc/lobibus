<?php
$pageCss = ['chatbot.css'];
$pageJs = ['chatbot.js'];
?>
<section class="chatbot-page">
    <div class="container">
        <div class="chatbot-header">
            <div>
                <span class="section-kicker">Hỗ trợ khách hàng</span>
                <h1>Trợ lý LobiBus</h1>
                <p>Nhận câu trả lời nhanh về đặt vé, thanh toán, hủy vé, đổi ghế, hành lý và check-in từ dữ liệu hỗ trợ của hệ thống.</p>
            </div>
            <div class="chatbot-service-card">
                <span>Trạng thái</span>
                <strong>Đang hoạt động</strong>
                <small>Phản hồi qua API `/api/chatbot/reply`</small>
            </div>
        </div>

        <div class="chatbot-layout">
            <aside class="chatbot-sidebar">
                <div class="assistant-card">
                    <div class="assistant-avatar">LB</div>
                    <div>
                        <strong>LobiBus Assistant</strong>
                        <span>Hỗ trợ tự động 24/7</span>
                    </div>
                </div>

                <div class="chatbot-sidebar-section">
                    <h2>Chủ đề thường hỏi</h2>
                    <div class="quick-question-grid">
                        <button type="button" class="quick-question" data-question="Làm sao để đặt vé?">
                            <strong>Đặt vé</strong>
                            <span>Quy trình chọn chuyến và giữ ghế</span>
                        </button>
                        <button type="button" class="quick-question" data-question="LobiBus hỗ trợ thanh toán gì?">
                            <strong>Thanh toán</strong>
                            <span>Tiền mặt, ví điện tử, chuyển khoản</span>
                        </button>
                        <button type="button" class="quick-question" data-question="Tôi có thể hủy vé không?">
                            <strong>Hủy vé</strong>
                            <span>Điều kiện hủy và hoàn tiền</span>
                        </button>
                        <button type="button" class="quick-question" data-question="Tôi check-in lên xe thế nào?">
                            <strong>Check-in</strong>
                            <span>Dùng mã vé hoặc mã QR</span>
                        </button>
                    </div>
                </div>

                <div class="chatbot-note">
                    <strong>Gợi ý nhập liệu</strong>
                    <p>Bạn có thể gõ có dấu hoặc không dấu, ví dụ “thanh toán” hoặc “thanh toan”.</p>
                </div>
            </aside>

            <div class="chat-window">
                <div class="chat-window-header">
                    <div>
                        <strong>Cuộc trò chuyện</strong>
                        <span>Dữ liệu lấy từ bảng `chatbot_questions`</span>
                    </div>
                    <span class="chat-status-dot">Online</span>
                </div>

                <div id="chatMessages" class="chat-messages" aria-live="polite">
                    <div class="chat-message bot">
                        <span>LobiBus</span>
                        <p>Xin chào, mình có thể hỗ trợ bạn về đặt vé, thanh toán, hủy vé, đổi ghế hoặc tra cứu thông tin chuyến xe.</p>
                    </div>
                </div>

                <form id="chatbotForm" class="chat-composer">
                    <input id="chatbotMessage" class="form-control" placeholder="Nhập câu hỏi của bạn..." autocomplete="off">
                    <button class="btn btn-success" type="submit">Gửi</button>
                </form>
            </div>
        </div>
    </div>
</section>
