(function () {
  const seatMap = document.getElementById('seatMap');
  const selectedSeatList = document.getElementById('selectedSeatList');
  const selectedSeatIdsInput = document.getElementById('selectedSeatIds');
  const totalAmount = document.getElementById('seatTotalAmount');
  const submitButton = document.getElementById('bookingSubmit');
  const form = document.getElementById('seatCheckoutForm');
  const base = window.APP_BASE_URL || '';

  if (!seatMap || !form) return;

  const tripId = seatMap.dataset.tripId || new URLSearchParams(window.location.search).get('trip_id');
  const selected = new Map();

  const money = (value) => `${Number(value || 0).toLocaleString('vi-VN')}đ`;

  function naturalSeatSort(a, b) {
    return String(a.seat_number || '').localeCompare(String(b.seat_number || ''), 'vi', {
      numeric: true,
      sensitivity: 'base',
    });
  }

  function inferBusType(seats, meta) {
    if (meta?.bus_type) return meta.bus_type;
    if (seats.some((seat) => String(seat.seat_number || '').startsWith('VIP'))) return 'limousine';
    if (seats.length >= 36) return 'sleeper';
    return 'standard';
  }

  function renderSummary() {
    const seats = Array.from(selected.values());
    selectedSeatIdsInput.value = seats.map((seat) => seat.seat_id).join(',');
    submitButton.disabled = seats.length === 0;

    if (!seats.length) {
      selectedSeatList.className = 'selected-seat-list text-muted mb-3';
      selectedSeatList.innerHTML = 'Chưa chọn ghế.';
      totalAmount.textContent = '0đ';
      return;
    }

    selectedSeatList.className = 'selected-seat-list mb-3';
    selectedSeatList.innerHTML = seats
      .sort(naturalSeatSort)
      .map((seat) => `<span class="selected-seat-pill">${seat.seat_number} <small>${money(seat.price)}</small></span>`)
      .join('');
    totalAmount.textContent = money(seats.reduce((sum, seat) => sum + Number(seat.price || 0), 0));
  }

  function seatButton(seat, extraClass = '') {
    if (!seat) {
      return '<span class="seat-placeholder" aria-hidden="true"></span>';
    }

    const isBooked = seat.status === 'booked';
    return `
      <button type="button"
              class="seat-button ${extraClass} ${isBooked ? 'booked' : ''}"
              data-seat-id="${seat.seat_id}"
              data-seat-number="${seat.seat_number}"
              data-price="${seat.price}"
              ${isBooked ? 'disabled' : ''}>
        <span>${seat.seat_number}</span>
        <small>${isBooked ? 'Đã đặt' : money(seat.price)}</small>
      </button>
    `;
  }

  function aisle() {
    return '<span class="seat-aisle" aria-hidden="true"></span>';
  }

  function vehicleHeader(label) {
    return `
      <div class="vehicle-front">
        <div class="vehicle-front-spacer"></div>
        <div class="vehicle-windshield">Đầu xe</div>
        <div class="vehicle-door vehicle-door-right">Cửa lên xuống</div>
        <div class="vehicle-layout-label">${label}</div>
      </div>
    `;
  }

  function renderStandard(seats) {
    const rows = [];
    for (let i = 0; i < seats.length; i += 4) {
      rows.push(seats.slice(i, i + 4));
    }

    return `
      <div class="vehicle-shell standard-layout">
        ${vehicleHeader('Sơ đồ ghế ngồi 2-2')}
        <div class="seat-row seat-row-head">
          <span>Cửa sổ trái</span>
          <span></span>
          <span>Lối đi</span>
          <span></span>
          <span>Cửa sổ phải</span>
        </div>
        ${rows.map((row) => `
          <div class="seat-row standard-seat-row">
            ${seatButton(row[0])}
            ${seatButton(row[1])}
            ${aisle()}
            ${seatButton(row[2])}
            ${seatButton(row[3])}
          </div>
        `).join('')}
      </div>
    `;
  }

  function renderSleeperDeck(deckSeats, title) {
    const rows = [];
    for (let i = 0; i < deckSeats.length; i += 2) {
      rows.push(deckSeats.slice(i, i + 2));
    }

    return `
      <div class="sleeper-deck">
        <div class="sleeper-deck-title">${title}</div>
        <div class="seat-row seat-row-head sleeper-head">
          <span>Dãy trái</span>
          <span>Lối đi</span>
          <span>Dãy phải</span>
        </div>
        ${rows.map((row) => `
          <div class="seat-row sleeper-seat-row">
            ${seatButton(row[0], 'sleeper-seat')}
            ${aisle()}
            ${seatButton(row[1], 'sleeper-seat')}
          </div>
        `).join('')}
      </div>
    `;
  }

  function renderSleeper(seats) {
    const half = Math.ceil(seats.length / 2);
    const lower = seats.slice(0, half);
    const upper = seats.slice(half);

    return `
      <div class="vehicle-shell sleeper-layout">
        ${vehicleHeader('Sơ đồ giường nằm 2 tầng')}
        <div class="sleeper-decks">
          ${renderSleeperDeck(lower, 'Tầng dưới')}
          ${renderSleeperDeck(upper, 'Tầng trên')}
        </div>
      </div>
    `;
  }

  function renderLimousine(seats) {
    const rows = [
      [seats[0], null, seats[1]],
      [seats[2], null, seats[3]],
      [seats[4], null, seats[5]],
      [seats[6], seats[7], seats[8]],
    ];

    return `
      <div class="vehicle-shell limousine-layout">
        ${vehicleHeader('Sơ đồ limousine VIP')}
        <div class="seat-row seat-row-head limousine-head">
          <span>Ghế đơn trái</span>
          <span>Lối đi</span>
          <span>Ghế đơn phải</span>
        </div>
        ${rows.map((row, index) => `
          <div class="seat-row limousine-seat-row ${index === rows.length - 1 ? 'limousine-back-row' : ''}">
            ${seatButton(row[0], 'vip-seat')}
            ${index === rows.length - 1 ? seatButton(row[1], 'vip-seat') : aisle()}
            ${seatButton(row[2], 'vip-seat')}
          </div>
        `).join('')}
      </div>
    `;
  }

  function renderSeats(seats, meta) {
    const sortedSeats = [...seats].sort(naturalSeatSort);
    if (!sortedSeats.length) {
      seatMap.innerHTML = '<div class="text-muted">Chuyến xe này chưa có dữ liệu ghế.</div>';
      return;
    }

    const busType = inferBusType(sortedSeats, meta);
    if (busType === 'sleeper') {
      seatMap.innerHTML = renderSleeper(sortedSeats);
      return;
    }

    if (busType === 'limousine') {
      seatMap.innerHTML = renderLimousine(sortedSeats);
      return;
    }

    seatMap.innerHTML = renderStandard(sortedSeats);
  }

  async function loadSeats() {
    try {
      const response = await fetch(`${base}/api/seats?trip_id=${encodeURIComponent(tripId || '')}`);
      const payload = await response.json();
      if (!response.ok) {
        throw new Error(payload.message || 'Không tải được danh sách ghế.');
      }
      renderSeats(payload.data || [], payload.meta || {});
    } catch (error) {
      seatMap.innerHTML = `<div class="alert alert-danger mb-0">${error.message}</div>`;
    }
  }

  seatMap.addEventListener('click', (event) => {
    const button = event.target.closest('.seat-button');
    if (!button || button.disabled) return;

    const seatId = button.dataset.seatId;
    if (selected.has(seatId)) {
      selected.delete(seatId);
      button.classList.remove('selected');
    } else {
      selected.set(seatId, {
        seat_id: seatId,
        seat_number: button.dataset.seatNumber,
        price: Number(button.dataset.price || 0),
      });
      button.classList.add('selected');
    }

    renderSummary();
  });

  form.addEventListener('submit', (event) => {
    if (!selected.size) {
      event.preventDefault();
      alert('Vui lòng chọn ít nhất một ghế.');
    }
  });

  renderSummary();
  loadSeats();
})();
