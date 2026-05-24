<!-- Chatbot Floating Trigger Button -->
<button id="chatbotTrigger" class="chatbot-trigger-btn animate-pop" aria-label="Mở trợ lý ảo LobiBus">
    <!-- Chat Icon (Bootstrap-like) -->
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-chat-dots-fill chat-icon" viewBox="0 0 16 16">
        <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
    </svg>
    <!-- Close Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-x-lg close-icon" viewBox="0 0 16 16" style="display: none;">
        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
    </svg>
</button>

<!-- Chatbot Floating Container -->
<div id="chatbotContainer" class="chatbot-floating-container">
    <div class="chat-window">
        <!-- Header -->
        <div class="chat-window-header">
            <div class="d-flex align-items-center gap-2">
                <div class="chatbot-header-avatar">AI</div>
                <div>
                    <strong>Trợ lý ảo LobiBus</strong>
                    <span class="chat-status-dot-floating">
                        <span class="ai-status-pulse"></span> Hoạt động
                    </span>
                </div>
            </div>
            <button id="chatbotCloseBtn" class="chat-close-btn" aria-label="Đóng chat">&times;</button>
        </div>

        <!-- Messages Area -->
        <div id="chatMessages" class="chat-messages" aria-live="polite">
            <div class="chat-message bot">
                <span>LobiBus</span>
                <p>Xin chào! Mình là Trợ lý ảo thông minh LobiBus. Mình đã kết nối trực tiếp với CSDL hệ thống.</p>
                <p>Bạn có thể hỏi mình bất kỳ điều gì liên quan đến chuyến xe, đặt vé, trạng thái thanh toán, đánh giá dịch vụ hoặc các chính sách khác nhé!</p>
            </div>
        </div>

        <!-- Suggestions Horizontal Row -->
        <div class="chat-suggestions-container">
            <span class="suggestions-title">💡 Gợi ý câu hỏi nhanh:</span>
            <div class="quick-question-row">
                <button type="button" class="quick-question-pill" data-question="Tuyến Hà Nội đi Hải Phòng có những chuyến nào?">
                    Tuyến Hà Nội - Hải Phòng
                </button>
                <button type="button" class="quick-question-pill" data-question="Tra cứu mã đặt vé LB-20260520-0001">
                    Tìm vé LB-20260520-0001
                </button>
                <button type="button" class="quick-question-pill" data-question="Kiểm tra các vé đã đặt của số điện thoại 0911000001">
                    Tìm vé qua SĐT
                </button>
                <button type="button" class="quick-question-pill" data-question="Xem các nhận xét và đánh giá gần đây của khách hàng">
                    Xem Đánh giá gần đây
                </button>
                <button type="button" class="quick-question-pill" data-question="Hệ thống đang chạy những tuyến đường nào?">
                    Các tuyến đang chạy
                </button>
            </div>
        </div>

        <!-- Composer Input -->
        <form id="chatbotForm" class="chat-composer">
            <input id="chatbotMessage" class="form-control" placeholder="Nhập câu hỏi của bạn..." autocomplete="off">
            <button class="btn btn-success" type="submit">Gửi</button>
        </form>
    </div>
</div>

<!-- Chatbot Background Overlay Backdrop -->
<div id="chatbotOverlay" class="chatbot-overlay"></div>
