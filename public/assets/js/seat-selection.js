(function () {
  const seatMap = document.getElementById('seatMap');
  const base = window.APP_BASE_URL || '';
  if (!seatMap) return;

  const params = new URLSearchParams(window.location.search);
  const tripId = params.get('trip_id') || '1';
  const selected = new Set();

  fetch(`${base}/api/seats?trip_id=${encodeURIComponent(tripId)}`)
    .then((response) => response.json())
    .then((payload) => {
      seatMap.innerHTML = (payload.data || []).map((seat) => `
        <button type="button" class="seat-button ${seat.status === 'booked' ? 'booked' : ''}" data-seat="${seat.seat_number}" ${seat.status === 'booked' ? 'disabled' : ''}>
          ${seat.seat_number}
        </button>
      `).join('');
    });

  seatMap.addEventListener('click', (event) => {
    const button = event.target.closest('.seat-button');
    if (!button || button.disabled) return;
    const code = button.dataset.seat;
    button.classList.toggle('selected');
    button.classList.contains('selected') ? selected.add(code) : selected.delete(code);
    window.selectedSeats = Array.from(selected);
  });
})();
