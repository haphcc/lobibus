(function () {
  const form = document.getElementById('chatbotForm');
  const input = document.getElementById('chatbotMessage');
  const messages = document.getElementById('chatMessages');
  const quickQuestions = document.querySelectorAll('.quick-question');
  const base = window.APP_BASE_URL || '';
  if (!form || !input || !messages) return;

  function parseMarkdown(text) {
    // Tránh tấn công XSS bằng cách escape các ký tự HTML nguy hiểm trước
    let html = text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    // Định dạng tiêu đề ###
    html = html.replace(/^###\s+(.+)$/gm, '<h5 style="margin-top: 14px; margin-bottom: 6px; font-weight: 700; color: #0f766e; font-size: 16px; border-bottom: 1px dashed #dcebe4; padding-bottom: 4px;">$1</h5>');

    // Định dạng chữ đậm **text**
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    // Định dạng mã code `code`
    html = html.replace(/`(.*?)`/g, '<code style="background-color: #e2ece7; color: #0f766e; padding: 2px 6px; border-radius: 4px; font-family: Consolas, monospace; font-size: 13.5px; font-weight: 600;">$1</code>');

    // Định dạng danh sách gạch đầu dòng - text
    html = html.replace(/^-\s+(.+)$/gm, '<div style="margin-left: 12px; margin-bottom: 4px; display: flex; align-items: flex-start; gap: 6px;"><span style="color: #0f766e;">•</span><span>$1</span></div>');

    // Đường kẻ phân tách ---
    html = html.replace(/^---\s*$/gm, '<hr style="border: 0; border-top: 1px solid #e2ece7; margin: 12px 0;">');

    // Đổi xuống dòng thành <br>
    html = html.replace(/\n/g, '<br>');

    // Xử lý các thẻ br liên tiếp để tránh khoảng trống thừa
    html = html.replace(/(<br>){3,}/g, '<br><br>');

    return html;
  }

  function addMessage(author, text, type) {
    const row = document.createElement('div');
    row.className = `chat-message ${type}`;

    const label = document.createElement('span');
    label.textContent = author;
    row.appendChild(label);

    const body = document.createElement('p');
    if (type.includes('bot') && !type.includes('loading')) {
      body.innerHTML = parseMarkdown(text);
    } else {
      body.textContent = text;
    }
    row.appendChild(body);

    messages.appendChild(row);
    // Cuộn mượt mà xuống cuối khung chat
    setTimeout(() => {
      messages.scrollTo({
        top: messages.scrollHeight,
        behavior: 'smooth'
      });
    }, 30);
    return row;
  }

  function addTyping() {
    const row = addMessage('LobiBus', 'Trợ lý LobiBus đang truy vấn dữ liệu...', 'bot loading');
    return () => row.remove();
  }

  async function sendMessage(text) {
    const message = text.trim();
    if (!message) return;

    addMessage('Bạn', message, 'user');
    input.value = '';
    input.focus();

    const removeTyping = addTyping();
    try {
      const response = await fetch(`${base}/api/chatbot/reply`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message }),
      });
      const payload = await response.json();
      removeTyping();
      addMessage('LobiBus', payload.reply || 'LobiBus chưa có câu trả lời phù hợp.', 'bot');
    } catch (error) {
      removeTyping();
      addMessage('LobiBus', 'Không kết nối được hệ thống hỗ trợ. Vui lòng thử lại sau.', 'bot error');
    }
  }

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    sendMessage(input.value);
  });

  quickQuestions.forEach((button) => {
    button.addEventListener('click', () => {
      sendMessage(button.dataset.question || button.textContent || '');
    });
  });
})();
