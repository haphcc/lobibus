(function () {
  const form = document.getElementById('tripSearchForm') || document.getElementById('bookingForm');
  const results = document.getElementById('tripResults');
  const base = window.APP_BASE_URL || '';
  const autoLoadScheduledTrips = !!(form && form.dataset && form.dataset.autoLoad === '1');
  // Return date toggle for round-trip: run after DOM ready to ensure elements exist
  window.addEventListener('DOMContentLoaded', () => {
    const roundTripRadio = document.getElementById('roundTrip');
    const returnWrapper = document.getElementById('returnDateWrapper');
    const returnInput = document.getElementById('returnDate');

    const updateReturnDateVisibility = () => {
      const isRound = !!(roundTripRadio && roundTripRadio.checked);
      if (!returnWrapper || !returnInput) return;
      if (isRound) {
        returnWrapper.style.display = '';
        returnInput.disabled = false;
        returnInput.required = true;
      } else {
        returnWrapper.style.display = 'none';
        returnInput.disabled = true;
        returnInput.required = false;
        returnInput.value = '';
      }
    };
    document.querySelectorAll('input[name="tripType"]').forEach(i => i.addEventListener('change', updateReturnDateVisibility));
    // initialize
    updateReturnDateVisibility();
  });
  if (!form || !results) return;

  const resultsSection = results.closest('section.booking-hero');
  // hide results area initially if empty
  if (resultsSection && !results.innerHTML.trim()) {
    resultsSection.style.display = 'none';
  }

  // Selected tickets area
  const selectedContainer = document.getElementById('selectedTickets');

  const STORAGE_KEY = 'lobibus_selected_tickets';

  function loadSelected() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch (e) {
      return [];
    }
  }

  function saveSelected(list) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    renderSelected();
  }

  function addSelected(trip) {
    const list = loadSelected();
    if (!list.find(t => String(t.id) === String(trip.id))) {
      list.push(trip);
      saveSelected(list);
    }
  }

  function removeSelected(tripId) {
    let list = loadSelected();
    list = list.filter(t => String(t.id) !== String(tripId));
    saveSelected(list);
  }

  function renderSelected() {
    if (!selectedContainer) return;
    const list = loadSelected();
    if (!list.length) {
      selectedContainer.innerHTML = '';
      return;
    }
    selectedContainer.innerHTML = `
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Vé đã chọn</h5>
          <div class="list-group">
            ${list.map(t => `
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>${t.from} → ${t.to}</strong>
                  <div><small class="text-muted">${t.departure_time || ''} • ${Number(t.price||0).toLocaleString('vi-VN')} VND</small></div>
                </div>
                <div class="btn-group">
                  <button class="btn btn-sm btn-primary js-go-seat" data-trip-id="${t.id}">Chọn ghế</button>
                  <button class="btn btn-sm btn-outline-danger js-remove-ticket" data-trip-id="${t.id}">Xóa</button>
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      </div>
    `;
  }

  // initialize selected list on load
  // Clear previous session selections on page load (F5 behavior)
  function clearSelected() {
    localStorage.removeItem(STORAGE_KEY);
    renderSelected();
  }
  clearSelected();

  // Also clear selections when user toggles trip type (one-way / roundtrip)
  document.addEventListener('change', (e) => {
    const el = e.target;
    if (el && el.name === 'tripType') {
      clearSelected();
    }
  });

  // delegate clicks for select ticket and selected area
  document.addEventListener('click', (ev) => {
    const target = ev.target;
    if (target.matches('.js-select-ticket')) {
      const tripJson = target.getAttribute('data-trip');
      try {
        const trip = JSON.parse(decodeURIComponent(tripJson));
        addSelected(trip);
      } catch (e) {
        console.error('Invalid trip data', e);
      }
      return;
    }
    if (target.matches('.js-go-seat')) {
      const tripId = target.getAttribute('data-trip-id');
      if (tripId) {
        window.location.href = `${base}/booking/select-seat?trip_id=${tripId}`;
      }
      return;
    }
    if (target.matches('.js-remove-ticket')) {
      const tripId = target.getAttribute('data-trip-id');
      if (tripId) removeSelected(tripId);
      return;
    }
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(form);
    const tripType = formData.get('tripType') || 'oneway';

    const renderSection = (title, trips) => {
      if (!trips || !trips.length) {
        return `<div class="col-12"><h5 class="mb-3">${title}</h5><p class="text-muted">Không tìm thấy chuyến nào.</p></div>`;
      }
      return `
        <div class="col-12"><h5 class="mb-3">${title}</h5></div>
        ${trips.map((trip) => `
          <div class="col-12 col-md-6">
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
                  <button class="btn btn-sm btn-primary js-select-ticket" data-trip="${encodeURIComponent(JSON.stringify({ id: trip.id, from: trip.from, to: trip.to, departure_time: trip.departure_time, price: trip.price }))}">Chọn vé</button>
                </div>
              </div>
            </article>
          </div>
        `).join('')}
      `;
    };

    if (tripType === 'roundtrip') {
      const from = formData.get('from');
      const to = formData.get('to');
      const departDate = formData.get('date');
      const returnDate = formData.get('return_date');
      const seats = formData.get('seats');

      const paramsOut = new URLSearchParams();
      paramsOut.set('from', from || '');
      paramsOut.set('to', to || '');
      if (departDate) paramsOut.set('date', departDate);
      if (seats) paramsOut.set('seats', seats);

      const paramsReturn = new URLSearchParams();
      paramsReturn.set('from', to || '');
      paramsReturn.set('to', from || '');
      if (returnDate) paramsReturn.set('date', returnDate);
      if (seats) paramsReturn.set('seats', seats);

      const [respOut, respReturn] = await Promise.all([
        fetch(`${base}/api/trips/search?${paramsOut.toString()}`),
        fetch(`${base}/api/trips/search?${paramsReturn.toString()}`)
      ]);
      const payloadOut = await respOut.json();
      const payloadReturn = await respReturn.json();

      results.innerHTML = '';
      results.innerHTML += renderSection('Chiều đi', payloadOut.data || []);
      results.innerHTML += renderSection('Chiều về', payloadReturn.data || []);
      if (resultsSection) resultsSection.style.display = '';
      return;
    }

    const params = new URLSearchParams(formData);
    const response = await fetch(`${base}/api/trips/search?${params.toString()}`);
    const payload = await response.json();
    results.innerHTML = renderSection('Kết quả', payload.data || []);
    if (resultsSection) resultsSection.style.display = '';
  });

  if (autoLoadScheduledTrips) {
    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
  }
})();
