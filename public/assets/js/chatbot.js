(function () {
  const form = document.getElementById('chatbotForm');
  const input = document.getElementById('chatbotMessage');
  const messages = document.getElementById('chatMessages');
  const quickQuestions = document.querySelectorAll('.quick-question');
  const base = window.APP_BASE_URL || '';
  if (!form || !input || !messages) return;

  function addMessage(author, text, type) {
    const row = document.createElement('div');
    row.className = `chat-message ${type}`;

    const label = document.createElement('span');
    label.textContent = author;
    row.appendChild(label);

    const body = document.createElement('p');
    body.textContent = text;
    row.appendChild(body);

    messages.appendChild(row);
    messages.scrollTop = messages.scrollHeight;
    return row;
  }

  function addTyping() {
    const row = addMessage('LobiBus', 'Đang tìm câu trả lời...', 'bot loading');
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
