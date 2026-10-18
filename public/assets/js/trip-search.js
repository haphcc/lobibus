(function () {
  const form = document.getElementById('tripSearchForm') || document.getElementById('bookingForm');
  const results = document.getElementById('tripResults');
  const base = window.APP_BASE_URL || '';
  if (!form || !results) return;

  const render = (trips) => {
    results.innerHTML = trips.map((trip) => `
      <div class="col-md-4">
        <article class="card h-100 shadow-sm">
          <div class="card-body">
            <h5>${trip.from} -> ${trip.to}</h5>
            <p class="mb-1">${trip.bus_name || 'LobiBus'}</p>
            <p class="mb-1">${trip.departure_time || ''}</p>
            <strong>${Number(trip.price || 0).toLocaleString('vi-VN')} VND</strong>
            <a class="btn btn-sm btn-success ms-2" href="${base}/booking/select-seat?trip_id=${trip.id}">Chon ghe</a>
          </div>
        </article>
      </div>
    `).join('');
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const params = new URLSearchParams(new FormData(form));
    const response = await fetch(`${base}/api/trips/search?${params.toString()}`);
    const payload = await response.json();
    render(payload.data || []);
  });
})();
