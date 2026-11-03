(function () {
  const button = document.getElementById('bookingSubmit');
  const base = window.APP_BASE_URL || '';
  if (!button) return;

  button.addEventListener('click', async () => {
    const payload = {
      trip_id: new URLSearchParams(window.location.search).get('trip_id') || 1,
      seats: window.selectedSeats || [],
    };

    const response = await fetch(`${base}/api/bookings/create`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': window.CSRF_TOKEN || '',
      },
      body: JSON.stringify(payload),
    });
    const result = await response.json();
    alert(result.message || 'Da gui yeu cau dat ve');
  });
})();
