(function () {
  const list = document.getElementById('recommendationList');
  const filters = document.querySelectorAll('.recommendation-filter');
  const base = window.APP_BASE_URL || '';
  let trips = [];
  let activeFilter = 'all';
  if (!list) return;

  function appendText(parent, tag, text, className) {
    const element = document.createElement(tag);
    if (className) element.className = className;
    element.textContent = text || '';
    parent.appendChild(element);
    return element;
  }

  function formatMoney(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')}đ`;
  }

  function formatDate(value) {
    if (!value) return '';
    const date = new Date(value.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleString('vi-VN', {
      hour: '2-digit',
      minute: '2-digit',
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  }

  function renderTrip(item) {
    const card = document.createElement('article');
    card.className = 'recommendation-card';

    const header = document.createElement('div');
    header.className = 'recommendation-card-header';
    appendText(header, 'span', item.reason || 'Gợi ý', 'recommendation-badge');
    appendText(header, 'strong', formatMoney(item.price), 'recommendation-price');
    card.appendChild(header);

    appendText(card, 'h2', item.route || 'Chuyến xe');
    appendText(card, 'p', item.bus_name || '', 'recommendation-bus');

    const meta = document.createElement('div');
    meta.className = 'recommendation-meta-new';

    const depFormatted = formatDate(item.departure_time);
    const arrFormatted = formatDate(item.arrival_time);
    
    const depParts = depFormatted.split(' ');
    const depTime = depParts[0] || '';
    const depDate = depParts[1] || '';

    const arrParts = arrFormatted.split(' ');
    const arrTime = arrParts[0] || '';
    const arrDate = arrParts[1] || '';

    meta.innerHTML = `
      <div class="journey-timeline">
        <div class="timeline-point departure">
          <i class="bi bi-circle-fill timeline-icon text-success"></i>
          <div class="timeline-details">
            <span class="timeline-label">Khởi hành</span>
            <div class="time-and-date">
              <strong class="time-highlight">${depTime}</strong>
              <span class="date-highlight">${depDate}</span>
            </div>
          </div>
        </div>
        <div class="timeline-line"></div>
        <div class="timeline-point arrival">
          <i class="bi bi-geo-alt-fill timeline-icon text-danger"></i>
          <div class="timeline-details">
            <span class="timeline-label">Đến nơi</span>
            <div class="time-and-date">
              <strong class="time-highlight">${arrTime}</strong>
              <span class="date-highlight">${arrDate}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="seats-info-badge mt-3">
        <i class="bi bi-person-workspace"></i>
        <span>Còn <strong class="seats-count text-success">${Number(item.available_seats || 0)}</strong> ghế trống</span>
      </div>
    `;
    card.appendChild(meta);

    const footer = document.createElement('div');
    footer.className = 'recommendation-card-footer';
    appendText(footer, 'span', `${Number(item.booking_count || 0)} lượt đặt`);

    const link = document.createElement('a');
    link.className = 'btn btn-success btn-sm';
    link.href = `${base}/booking/select-seat?trip_id=${encodeURIComponent(item.trip_id || '')}`;
    link.textContent = 'Chọn ghế';
    footer.appendChild(link);
    card.appendChild(footer);

    return card;
  }

  const price1 = document.getElementById('price1');
  const price2 = document.getElementById('price2');
  const price3 = document.getElementById('price3');
  const time1 = document.getElementById('time1');
  const time2 = document.getElementById('time2');
  const time3 = document.getElementById('time3');

  function getHour(dateTimeStr) {
    if (!dateTimeStr) return 0;
    const date = new Date(dateTimeStr.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return 0;
    return date.getHours();
  }

  function render() {
    let visibleTrips = activeFilter === 'all'
      ? trips
      : trips.filter((item) => item.reason === activeFilter);

    // Filter by Price
    if (price1?.checked || price2?.checked || price3?.checked) {
      visibleTrips = visibleTrips.filter((item) => {
        const p = Number(item.price || 0);
        if (price1?.checked && p < 200000) return true;
        if (price2?.checked && p >= 200000 && p <= 500000) return true;
        if (price3?.checked && p > 500000) return true;
        return false;
      });
    }

    // Filter by Time
    if (time1?.checked || time2?.checked || time3?.checked) {
      visibleTrips = visibleTrips.filter((item) => {
        const hour = getHour(item.departure_time);
        if (time1?.checked && hour >= 6 && hour < 12) return true;
        if (time2?.checked && hour >= 12 && hour < 18) return true;
        if (time3?.checked && hour >= 18 && hour < 24) return true;
        return false;
      });
    }

    list.innerHTML = '';

    if (visibleTrips.length === 0) {
      appendText(list, 'p', 'Không tìm thấy chuyến xe nào phù hợp với bộ lọc.', 'recommendation-empty');
      return;
    }

    visibleTrips.forEach((item) => list.appendChild(renderTrip(item)));
  }

  // Add event listeners to checkboxes
  [price1, price2, price3, time1, time2, time3].forEach(cb => {
    if (cb) cb.addEventListener('change', render);
  });

  filters.forEach((button) => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter || 'all';
      filters.forEach((filter) => filter.classList.remove('active'));
      button.classList.add('active');
      render();
    });
  });

  fetch(`${base}/api/recommendations`)
    .then((response) => response.json())
    .then((payload) => {
      trips = payload.data || [];
      render();
    })
    .catch(() => {
      list.innerHTML = '';
      appendText(list, 'p', 'Không tải được danh sách gợi ý.', 'recommendation-empty text-danger');
    });
})();
