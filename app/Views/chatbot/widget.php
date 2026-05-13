<?php $pageJs = ['chatbot.js']; ?>
<section class="container py-5" style="max-width:720px;">
    <h1>Chatbot hỗ trợ</h1>
    <div id="chatMessages" class="border rounded p-3 mb-3" style="min-height:220px;"></div>
    <form id="chatbotForm" class="d-flex gap-2">
        <input id="chatbotMessage" class="form-control" placeholder="Nhập câu hỏi...">
        <button class="btn btn-success">Gửi</button>
    </form>
    <a href="/" class="d-block mt-3">Quay về trang chủ</a>
</section>
