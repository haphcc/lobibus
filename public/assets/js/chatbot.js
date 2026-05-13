(function () {
  const form = document.getElementById('chatbotForm');
  const input = document.getElementById('chatbotMessage');
  const messages = document.getElementById('chatMessages');
  const base = window.APP_BASE_URL || '';
  if (!form || !input || !messages) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    messages.insertAdjacentHTML('beforeend', `<div><strong>Ban:</strong> ${text}</div>`);
    input.value = '';

    const response = await fetch(`${base}/api/chatbot/reply`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text }),
    });
    const payload = await response.json();
    messages.insertAdjacentHTML('beforeend', `<div><strong>LobiBus:</strong> ${payload.reply}</div>`);
  });
})();
