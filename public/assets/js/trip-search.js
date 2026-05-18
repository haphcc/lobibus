(function () {
  const form = document.getElementById('tripSearchForm') || document.getElementById('bookingForm');
  const results = document.getElementById('tripResults');
  const base = window.APP_BASE_URL || '';
  if (!form || !results) return;

  const resultsSection = results.closest('section.booking-hero');
  // hide results area initially if empty
  if (resultsSection && !results.innerHTML.trim()) {
    resultsSection.style.display = 'none';
  }

  const render = (trips) => {
    results.innerHTML = trips.map((trip) => `
      <div class="col-12">
        <article class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">${trip.from} → ${trip.to}</h5>
            <hr>
            <div class="mb-3">
              <p class="mb-1"><small class="text-muted">Xe:</small> <strong>${trip.bus_name || 'LobiBus'}</strong></p>
              <p class="mb-1"><small class="text-muted">Khởi hành:</small> <strong>${trip.departure_time || ''}</strong></p>
              <p class="mb-1"><small class="text-muted">Tới nơi:</small> <strong>${trip.arrival_time || ''}</strong></p>
              <p class="mb-2"><small class="text-muted">Ghế trống:</small> <strong>${trip.available_seats || 0}</strong></p>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="h5 mb-0">${Number(trip.price || 0).toLocaleString('vi-VN')} VND</span>
              <a class="btn btn-sm btn-success" href="${base}/booking/select-seat?trip_id=${trip.id}">Chọn ghế</a>
            </div>
          </div>
        </article>
      </div>
    `).join('');
    if (resultsSection) {
      resultsSection.style.display = (trips && trips.length) ? '' : 'none';
    }
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const params = new URLSearchParams(new FormData(form));
    const response = await fetch(`${base}/api/trips/search?${params.toString()}`);
    const payload = await response.json();
    render(payload.data || []);
  });
})();
