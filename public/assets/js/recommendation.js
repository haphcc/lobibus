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
    meta.className = 'recommendation-meta';
    appendText(meta, 'span', `Khởi hành: ${formatDate(item.departure_time)}`);
    appendText(meta, 'span', `Đến nơi: ${formatDate(item.arrival_time)}`);
    appendText(meta, 'span', `Ghế trống: ${Number(item.available_seats || 0)}`);
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

  function render() {
    const visibleTrips = activeFilter === 'all'
      ? trips
      : trips.filter((item) => item.reason === activeFilter);

    list.innerHTML = '';

    if (visibleTrips.length === 0) {
      appendText(list, 'p', 'Chưa có gợi ý chuyến xe phù hợp.', 'recommendation-empty');
      return;
    }

    visibleTrips.forEach((item) => list.appendChild(renderTrip(item)));
  }

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
