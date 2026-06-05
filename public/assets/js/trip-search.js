(function () {
  const form = document.getElementById('tripSearchForm');
  const resultsContainer = document.getElementById('tripResults');
  const selectedContainer = document.getElementById('selectedTickets');
  const base = window.APP_BASE_URL || '';
  const STORAGE_KEY = 'lobibus_selected_tickets';
  const GROUP_KEY = 'lobibus_roundtrip_group_code';

  let allOutboundTrips = []; // Raw outbound trips from API
  let allReturnTrips = []; // Raw return trips from API
  const DATE_RESULT_LIMIT = 1500;
  const DATE_STRIP_LIMIT = 500;
  const MAX_VISIBLE_ROUTES = 6;
  const MAX_TRIPS_PER_ROUTE = 5;
  let activeTimeFilters = []; // ['morning', 'afternoon', 'evening']
  let activeTypeFilters = []; // ['vip', 'sleeper', 'seat']
  let currentSort = 'time-asc'; // 'time-asc', 'time-desc', 'price-asc', 'price-desc'

  // Selected ticket utilities
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

  function roundTripGroupCode() {
    let code = localStorage.getItem(GROUP_KEY) || '';
    if (!code) {
      code = `RT-${new Date().toISOString().slice(0, 10).replace(/-/g, '')}-${Math.random().toString(16).slice(2, 8).toUpperCase()}`;
      localStorage.setItem(GROUP_KEY, code);
    }
    return code;
  }

  function addSelected(trip) {
    const list = loadSelected();
    const tripType = document.querySelector('input[name="tripType"]:checked')?.value || 'oneway';
    const direction = trip.direction || 'outbound';
    const enriched = {
      ...trip,
      trip_type: tripType,
      direction: tripType === 'roundtrip' ? direction : 'outbound',
      booking_group_code: tripType === 'roundtrip' ? roundTripGroupCode() : ''
    };

    let next = list.filter(t => String(t.id) !== String(enriched.id));
    if (tripType === 'roundtrip') {
      next = next.filter(t => t.direction !== enriched.direction);
    }
    next.push(enriched);
    saveSelected(next);
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
      <div class="card animated-item">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-ticket-perforated-fill me-2"></i>Vé đã chọn</h5>
          <div class="list-group">
            ${list.map(t => {
              const depDate = new Date(t.departure_time);
              const depTimeStr = depDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
              const depDateStr = depDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
              const directionLabel = t.trip_type === 'roundtrip'
                ? (t.direction === 'return' ? 'Chiều về' : 'Chiều đi')
                : 'Một chiều';
              const nextTrip = t.trip_type === 'roundtrip'
                ? list.find(item => item.direction && item.direction !== t.direction)
                : null;
              const canChooseSeat = t.trip_type !== 'roundtrip' || !!nextTrip;
              return `
                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle mb-2">${directionLabel}</span>
                    <strong class="fs-6 text-dark">${t.from} <i class="bi bi-arrow-right mx-1 text-muted"></i> ${t.to}</strong>
                    <div class="mt-1">
                      <small class="text-secondary">
                        <i class="bi bi-clock me-1"></i>${depTimeStr} • ${depDateStr} • <strong>${Number(t.price||0).toLocaleString('vi-VN')}đ</strong>
                      </small>
                    </div>
                  </div>
                  <div class="btn-group">
                    <button class="btn btn-sm btn-success js-go-seat"
                            data-trip-id="${t.id}"
                            data-trip-type="${t.trip_type || 'oneway'}"
                            data-direction="${t.direction || 'outbound'}"
                            data-booking-group-code="${t.booking_group_code || ''}"
                            data-next-trip-id="${nextTrip ? nextTrip.id : ''}"
                            data-next-direction="${nextTrip ? nextTrip.direction : ''}"
                            ${canChooseSeat ? '' : 'disabled'}
                            style="background-color: #0f766e; border-color: #0f766e;">Chọn ghế</button>
                    <button class="btn btn-sm btn-outline-danger js-remove-ticket" data-trip-id="${t.id}">Xóa</button>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
        </div>
      </div>
    `;
  }

  function clearSelected() {
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(GROUP_KEY);
    renderSelected();
  }

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    // Clear previous session selections on page load (F5 behavior)
    clearSelected();

    // 1. Initial Statistics loading
    loadInitialStats();

    // 2. Return date input toggle behavior
    const roundTripRadio = document.getElementById('roundTrip');
    const returnWrapper = document.getElementById('returnDateWrapper');
    const returnInput = document.getElementById('returnDate');

    const updateReturnDateVisibility = () => {
      const isRound = !!(roundTripRadio && roundTripRadio.checked);
      if (!returnWrapper || !returnInput) return;
      if (isRound) {
        returnInput.disabled = false;
        // returnInput.required = true; // Bỏ bắt buộc nhập ngày về
        returnWrapper.style.pointerEvents = 'auto';
        returnWrapper.style.opacity = '1';
      } else {
        returnInput.disabled = true;
        returnInput.required = false;
        returnInput.value = '';
        returnWrapper.style.pointerEvents = 'none';
        returnWrapper.style.opacity = '0.65';
      }
    };
    document.querySelectorAll('input[name="tripType"]').forEach(i => i.addEventListener('change', () => {
      updateReturnDateVisibility();
      clearSelected();
    }));
    updateReturnDateVisibility();

    // 3. Swap button
    const swapBtn = document.getElementById('swapLocationsBtn');
    if (swapBtn) {
      swapBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const fromSelect = document.getElementById('fromLocationSelect');
        const toSelect = document.getElementById('toLocationSelect');
        if (fromSelect && toSelect) {
          const temp = fromSelect.value;
          fromSelect.value = toSelect.value;
          toSelect.value = temp;
        }
      });
    }

    // 4. Set up filters
    document.querySelectorAll('.time-pill-btn input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        const pill = checkbox.closest('.time-pill-btn');
        if (checkbox.checked) {
          pill.classList.add('active');
          if (!activeTimeFilters.includes(checkbox.value)) activeTimeFilters.push(checkbox.value);
        } else {
          pill.classList.remove('active');
          activeTimeFilters = activeTimeFilters.filter(v => v !== checkbox.value);
        }
        applyFiltersAndRender();
      });
    });

    document.querySelectorAll('.type-pill-btn input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        const pill = checkbox.closest('.type-pill-btn');
        if (checkbox.checked) {
          pill.classList.add('active');
          if (!activeTypeFilters.includes(checkbox.value)) activeTypeFilters.push(checkbox.value);
        } else {
          pill.classList.remove('active');
          activeTypeFilters = activeTypeFilters.filter(v => v !== checkbox.value);
        }
        applyFiltersAndRender();
      });
    });

    // 5. Set up sorting
    const sortSelect = document.getElementById('sortBySelect');
    if (sortSelect) {
      sortSelect.addEventListener('change', (e) => {
        currentSort = e.target.value;
        applyFiltersAndRender();
      });
    }

    // 6. Pre-fill Search parameters if on Search results page
    const isSearchPage = window.location.pathname.includes('/trips/search');
    if (isSearchPage) {
      const params = new URLSearchParams(window.location.search);
      const fromVal = params.get('from') || '';
      const toVal = params.get('to') || '';
      const dateVal = params.get('date') || '';
      const returnDateVal = params.get('return_date') || '';
      const seatsVal = params.get('seats') || '1';
      const tripTypeVal = params.get('tripType') || 'oneway';

      const fromSelect = document.getElementById('fromLocationSelect');
      const toSelect = document.getElementById('toLocationSelect');
      const dateInput = document.getElementById('dateInput');
      const seatsSelect = document.getElementById('seatsSelect');
      const oneWayRadio = document.getElementById('oneWay');
      const roundTripRadio = document.getElementById('roundTrip');

      if (fromSelect) fromSelect.value = fromVal;
      if (toSelect) toSelect.value = toVal;
      if (dateInput) dateInput.value = dateVal;
      if (returnInput) returnInput.value = returnDateVal;
      if (seatsSelect) seatsSelect.value = seatsVal;
      if (tripTypeVal === 'roundtrip' && roundTripRadio) {
        roundTripRadio.checked = true;
      } else if (oneWayRadio) {
        oneWayRadio.checked = true;
      }
      updateReturnDateVisibility();

      if (fromVal && toVal && dateVal) {
        performSearch(fromVal, toVal, dateVal, returnDateVal, seatsVal, tripTypeVal);
      }
    }

    // 7. Setup Date Strip navigation prev/next arrows
    const prevBtn = document.getElementById('datePrevBtn');
    const nextBtn = document.getElementById('dateNextBtn');

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        const dateInput = document.getElementById('dateInput');
        if (!dateInput || !dateInput.value) return;
        const newDate = addDays(dateInput.value, -1);
        dateInput.value = newDate;
        
        // Update URL query string
        const newUrl = updateQueryStringParameter(window.location.href, 'date', newDate);
        window.history.pushState({ path: newUrl }, '', newUrl);

        // Re-execute search from fields
        triggerSearchFromForm();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        const dateInput = document.getElementById('dateInput');
        if (!dateInput || !dateInput.value) return;
        const newDate = addDays(dateInput.value, 1);
        dateInput.value = newDate;
        
        // Update URL query string
        const newUrl = updateQueryStringParameter(window.location.href, 'date', newDate);
        window.history.pushState({ path: newUrl }, '', newUrl);

        // Re-execute search from fields
        triggerSearchFromForm();
      });
    }

    function triggerSearchFromForm() {
      const form = document.getElementById('tripSearchForm');
      if (form) {
        const formData = new FormData(form);
        const from = formData.get('from');
        const to = formData.get('to');
        const date = formData.get('date');
        const returnDate = formData.get('return_date');
        const seats = formData.get('seats');
        const tripType = formData.get('tripType') || 'oneway';
        performSearch(from, to, date, returnDate, seats, tripType);
      }
    }
  });

  // Handle Event Delegation
  document.addEventListener('click', (ev) => {
    const target = ev.target;
    
    // View Map Modal Action
    const mapBtn = target.closest('.js-view-map');
    if (mapBtn) {
      ev.preventDefault();
      if (window.LobiBusMap) {
        window.LobiBusMap.showMap(
          mapBtn.getAttribute('data-from'),
          mapBtn.getAttribute('data-from-addr'),
          mapBtn.getAttribute('data-from-lat'),
          mapBtn.getAttribute('data-from-lng'),
          mapBtn.getAttribute('data-to'),
          mapBtn.getAttribute('data-to-addr'),
          mapBtn.getAttribute('data-to-lat'),
          mapBtn.getAttribute('data-to-lng')
        );
      }
      return;
    }

    // Select Ticket Action
    const selectBtn = target.closest('.js-select-ticket');
    if (selectBtn) {
      const tripJson = selectBtn.getAttribute('data-trip');
      try {
        const trip = JSON.parse(decodeURIComponent(tripJson));
        addSelected(trip);
      } catch (e) {
        console.error('Mã hóa dữ liệu chuyến đi bị lỗi:', e);
      }
      return;
    }

    // Select Seat Redirect Action
    const goSeatBtn = target.closest('.js-go-seat');
    if (goSeatBtn) {
      const tripId = goSeatBtn.getAttribute('data-trip-id');
      if (tripId) {
        const params = new URLSearchParams({ trip_id: tripId });
        const selectedSeatCount = document.getElementById('seatsSelect')?.value || new URLSearchParams(window.location.search).get('seats') || '1';
        params.set('seats', selectedSeatCount);
        params.set('return_url', `${window.location.pathname}${window.location.search}`);
        const tripType = goSeatBtn.getAttribute('data-trip-type') || 'oneway';
        if (tripType === 'roundtrip') {
          params.set('trip_type', 'roundtrip');
          params.set('direction', goSeatBtn.getAttribute('data-direction') || 'outbound');
          params.set('booking_group_code', goSeatBtn.getAttribute('data-booking-group-code') || roundTripGroupCode());
          if (goSeatBtn.getAttribute('data-next-trip-id')) {
            params.set('next_trip_id', goSeatBtn.getAttribute('data-next-trip-id'));
            params.set('next_direction', goSeatBtn.getAttribute('data-next-direction') || '');
          }
        }
        window.location.href = `${base}/booking/select-seat?${params.toString()}`;
      }
      return;
    }

    // Remove selected ticket Action
    const removeBtn = target.closest('.js-remove-ticket');
    if (removeBtn) {
      const tripId = removeBtn.getAttribute('data-trip-id');
      if (tripId) removeSelected(tripId);
      return;
    }
  });

  // Fetch Stats dynamically on load
  async function loadInitialStats() {
    try {
      const response = await fetch(`${base}/api/trips/search`);
      const payload = await response.json();
      const trips = payload.data || [];
      
      const routeSet = new Set();
      trips.forEach(t => routeSet.add(t.route_id));
      
      const countRoutes = document.getElementById('statRouteCount');
      const countTrips = document.getElementById('statTripCount');
      
      if (countRoutes) countRoutes.innerText = routeSet.size;
      if (countTrips) countTrips.innerText = trips.length;
    } catch (e) {
      console.error('Lỗi khi nạp dữ liệu thống kê ban đầu:', e);
    }
  }

  function showLoadingState() {
    resultsContainer.innerHTML = `
      <div class="col-12 text-center py-5">
        <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem; color: #0f766e !important;">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="text-muted mt-3">Đang tìm chuyến xe tối ưu từ LobiBus...</p>
      </div>
    `;
  }

  function showErrorState() {
    resultsContainer.innerHTML = `
      <div class="col-12 text-center py-5 text-danger">
        <i class="bi bi-exclamation-triangle-fill display-4" style="color: #0f766e;"></i>
        <h4 class="mt-3">Không thể kết nối hệ thống</h4>
        <p class="text-muted">Đã xảy ra sự cố tải chuyến xe. Quý khách vui lòng thử lại sau.</p>
      </div>
    `;
  }

  // Categorize bus type helper
  function getBusType(busName) {
    const name = (busName || '').toLowerCase();
    if (name.includes('limousine') || name.includes('vip') || name.includes('luxury')) {
      return { id: 'vip', label: 'VIP Limousine', badgeClass: 'badge-vip', icon: 'bi-gem', img: 'images/bus/limousine.jpg' };
    } else if (name.includes('giường') || name.includes('sleeper') || name.includes('nằm')) {
      return { id: 'sleeper', label: 'Giường nằm', badgeClass: 'badge-sleeper', icon: 'bi-moon-stars', img: 'images/bus/giuong-nam.png' };
    } else {
      return { id: 'seat', label: 'Ghế ngồi', badgeClass: 'badge-seating', icon: 'bi-person-workspace', img: 'images/bus/ghe-ngoi.jpg' };
    }
  }

  // Check if departure time is in range
  function isTimeInRange(timeStr, range) {
    if (!timeStr) return false;
    const parts = timeStr.split(' ');
    if (parts.length < 2) return false;
    const timeOnly = parts[1];
    const hour = parseInt(timeOnly.split(':')[0], 10);

    if (range === 'morning') return hour >= 0 && hour < 12;
    if (range === 'afternoon') return hour >= 12 && hour < 18;
    if (range === 'evening') return hour >= 18 && hour < 24;
    return false;
  }

  // Handle Form Search Submit
  if (form) {
    form.addEventListener('submit', async (e) => {
      // Đặt mặc định ngày hôm nay nếu người dùng không chọn ngày đi
      const dateInput = form.querySelector('input[name="date"]');
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      const todayStr = `${year}-${month}-${day}`;

      if (dateInput && !dateInput.value) {
        dateInput.value = todayStr;
      }

      // Bỏ tự động điền ngày về để tránh bắt chọn ngày về trên trang chủ
      const isSearchPage = window.location.pathname.includes('/trips/search');
      if (!isSearchPage) {
        // Let homepage form submit naturally to search page
        return;
      }

      e.preventDefault();
      const formData = new FormData(form);
      const tripType = formData.get('tripType') || 'oneway';

      const from = formData.get('from');
      const to = formData.get('to');
      let date = formData.get('date');
      const returnDate = formData.get('return_date');
      const seats = formData.get('seats');

      // Update URL query string dynamically
      const newUrl = `${window.location.pathname}?tripType=${tripType}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&date=${date}&return_date=${returnDate}&seats=${seats}`;
      window.history.pushState({ path: newUrl }, '', newUrl);

      performSearch(from, to, date, returnDate, seats, tripType);
    });
  }

  function processTrips(trips, activeDate) {
    let filtered = trips.filter(trip => {
      // Date filter: Show trips on or after activeDate (YYYY-MM-DD match)
      if (activeDate) {
        const tripDate = trip.departure_time.split(' ')[0];
        if (tripDate < activeDate) {
          return false;
        }
      }
      
      // Time filter
      if (activeTimeFilters.length > 0) {
        const matchesTime = activeTimeFilters.some(range => isTimeInRange(trip.departure_time, range));
        if (!matchesTime) return false;
      }

      // Bus Type filter
      if (activeTypeFilters.length > 0) {
        const typeInfo = getBusType(trip.bus_name);
        if (!activeTypeFilters.includes(typeInfo.id)) return false;
      }

      return true;
    });

    // Sort
    filtered.sort((a, b) => {
      if (currentSort === 'time-asc') {
        return new Date(a.departure_time) - new Date(b.departure_time);
      }
      if (currentSort === 'time-desc') {
        return new Date(b.departure_time) - new Date(a.departure_time);
      }
      if (currentSort === 'price-asc') {
        return parseFloat(a.price) - parseFloat(b.price);
      }
      if (currentSort === 'price-desc') {
        return parseFloat(b.price) - parseFloat(a.price);
      }
      return 0;
    });

    return filtered;
  }

  function normalizeRouteKey(value) {
    return String(value || '')
      .trim()
      .toLowerCase()
      .replace(/\s+/g, ' ');
  }

  function groupTripsByRoute(trips) {
    const groups = {};
    trips.forEach(trip => {
      const key = `${normalizeRouteKey(trip.from)}|${normalizeRouteKey(trip.to)}`;
      if (!groups[key]) {
        groups[key] = {
          route_id: trip.route_id,
          from: trip.from,
          to: trip.to,
          distance_km: trip.distance_km,
          duration_minutes: trip.duration_minutes,
          trips: [],
          minPrice: parseFloat(trip.price)
        };
      }
      groups[key].trips.push(trip);
      if (parseFloat(trip.price) < groups[key].minPrice) {
        groups[key].minPrice = parseFloat(trip.price);
      }
    });
    return Object.values(groups).map(route => ({
      ...route,
      trips: route.trips // Removed slice to allow Load More
    }));
  }

  function selectVisibleGroups(groups) {
    // Trả về toàn bộ tuyến đường để có thể sử dụng "Xem thêm tuyến"
    return groups;
  }

  function applyFiltersAndRender() {
    const activeDate = document.getElementById('dateInput')?.value || '';
    const outboundProcessed = processTrips(allOutboundTrips, activeDate);
    const returnDate = document.getElementById('returnDate')?.value || activeDate;
    const returnProcessed = processTrips(allReturnTrips, returnDate);

    const tripType = document.querySelector('input[name="tripType"]:checked')?.value || 'oneway';

    resultsContainer.innerHTML = '';

    if (outboundProcessed.length === 0 && (tripType === 'oneway' || returnProcessed.length === 0)) {
      renderEmptyState();
      return;
    }

    if (tripType === 'roundtrip') {
      // Outbound Section
      const outboundHeader = document.createElement('div');
      outboundHeader.className = 'col-12 mt-3';
      outboundHeader.innerHTML = `<h4 class="fw-bold mb-3" style="color: #0f766e;"><i class="bi bi-arrow-right-circle-fill me-2"></i>Chiều đi</h4>`;
      resultsContainer.appendChild(outboundHeader);
      
      const outboundGroups = groupTripsByRoute(outboundProcessed);
      if (outboundGroups.length === 0) {
        const emptyOutbound = document.createElement('div');
        emptyOutbound.className = 'col-12 mb-4 text-muted text-center py-4 bg-light rounded-3 border';
        emptyOutbound.innerText = 'Không tìm thấy chuyến đi nào cho chiều đi.';
        resultsContainer.appendChild(emptyOutbound);
      } else {
        renderGroupsToContainer(selectVisibleGroups(outboundGroups), 'outbound');
      }

      // Return Section
      const returnHeader = document.createElement('div');
      returnHeader.className = 'col-12 mt-4';
      returnHeader.innerHTML = `<h4 class="fw-bold mb-3" style="color: #0f766e;"><i class="bi bi-arrow-left-circle-fill me-2"></i>Chiều về</h4>`;
      resultsContainer.appendChild(returnHeader);

      const returnGroups = groupTripsByRoute(returnProcessed);
      if (returnGroups.length === 0) {
        const emptyReturn = document.createElement('div');
        emptyReturn.className = 'col-12 mb-4 text-muted text-center py-4 bg-light rounded-3 border';
        emptyReturn.innerText = 'Không tìm thấy chuyến đi nào cho chiều về.';
        resultsContainer.appendChild(emptyReturn);
      } else {
        renderGroupsToContainer(selectVisibleGroups(returnGroups), 'return');
      }
    } else {
      // Oneway Section
      const outboundGroups = groupTripsByRoute(outboundProcessed);
      renderGroupsToContainer(selectVisibleGroups(outboundGroups), 'outbound');
    }
  }

  function renderGroupsToContainer(groups, direction = 'outbound') {
    groups.forEach((route, index) => {
      let durationStr = 'Đang cập nhật';
      if (route.duration_minutes) {
        const hours = Math.floor(route.duration_minutes / 60);
        const mins = route.duration_minutes % 60;
        durationStr = hours > 0 ? `${hours} giờ ${mins > 0 ? mins + ' phút' : ''}` : `${mins} phút`;
      }
      
      const distanceStr = route.distance_km ? `${route.distance_km} km` : 'Chưa xác định';
      const routeCard = document.createElement('div');
      routeCard.className = `col-12 route-group-card animated-item ${index >= 3 ? 'd-none route-hidden' : ''}`;
      routeCard.style.animationDelay = `${index * 0.08}s`;
      
      routeCard.innerHTML = `
        <div class="route-group-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" data-route-index="${index}">
          <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
              <span class="route-badge"><i class="bi bi-signpost-split-fill me-1"></i>Tuyến xe cố định</span>
              <span class="route-duration-badge"><i class="bi bi-clock-history"></i>${durationStr}</span>
              ${route.distance_km ? `<span class="badge bg-light text-secondary border"><i class="bi bi-geo-alt-fill me-1 text-danger"></i>${distanceStr}</span>` : ''}
            </div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2 flex-wrap" style="color: #111827;">
              <span>${route.from}</span>
              <span class="text-muted fs-5"><i class="bi bi-arrow-right"></i></span>
              <span>${route.to}</span>
            </h4>
          </div>
          <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-4 w-100-mobile">
            <div class="text-md-end">
              <small class="text-muted d-block">Giá vé chỉ từ</small>
              <strong class="fs-5 text-success">${route.minPrice.toLocaleString('vi-VN')}đ</strong>
            </div>
            <div class="d-flex align-items-center gap-3">
              <span class="badge bg-success-subtle text-success px-3 py-2 rounded-3 border border-success-subtle">
                <strong>${route.trips.length}</strong> giờ chạy
              </span>
              <button class="btn btn-light rounded-circle shadow-sm border p-0 d-flex align-items-center justify-content-center chevron-btn" style="width: 38px; height: 38px;">
                <i class="bi bi-chevron-down chevron-icon fs-6"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="route-group-body">
          <div class="table-responsive">
            <table class="table timetable-table align-middle">
              <thead>
                <tr>
                  <th scope="col" style="width: 22%;">Giờ chạy (Dự kiến)</th>
                  <th scope="col" style="width: 25%;">Dòng xe LobiBus</th>
                  <th scope="col" style="width: 18%;">Ghế trống</th>
                  <th scope="col" style="width: 18%;">Giá vé</th>
                  <th scope="col" class="text-end" style="width: 17%;">Hành động</th>
                </tr>
              </thead>
              <tbody>
                ${route.trips.map((trip, tripIndex) => {
                  const typeInfo = getBusType(trip.bus_name);
                  
                  const depDate = new Date(trip.departure_time);
                  const arrDate = new Date(trip.arrival_time);
                  
                  const depTimeStr = depDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                  const arrTimeStr = arrDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                  const depDateStr = depDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
                  
                  const seats = parseInt(trip.available_seats || 0, 10);
                  let seatClass = 'seat-status-high';
                  let seatLabel = 'Còn nhiều ghế';
                  if (seats <= 3) {
                    seatClass = 'seat-status-low';
                    seatLabel = 'Chỉ còn ' + seats + ' ghế';
                  } else if (seats <= 10) {
                    seatClass = 'seat-status-medium';
                    seatLabel = 'Còn ' + seats + ' ghế';
                  } else {
                    seatLabel = 'Còn ' + seats + ' ghế';
                  }

                  const tripData = encodeURIComponent(JSON.stringify({
                    id: trip.id,
                    from: trip.from,
                    to: trip.to,
                    direction,
                    departure_time: trip.departure_time,
                    price: trip.price
                  }));

                  return `
                    <tr class="timetable-row ${tripIndex >= 2 ? 'd-none' : ''}">
                      <td data-label="Giờ chạy">
                        <div class="text-end text-md-start">
                          <div>
                            <span class="fw-bold fs-5 text-dark">${depTimeStr}</span>
                            <span class="text-muted mx-2">➔</span>
                            <span class="text-secondary fw-semibold">${arrTimeStr}</span>
                          </div>
                          <div class="mt-1">
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${depDateStr}</small>
                          </div>
                          <div class="mt-2">
                            <a href="#" class="text-teal text-decoration-none fw-semibold small js-view-map d-inline-flex align-items-center gap-1"
                               style="color: #0f766e;"
                               data-from="${trip.from}" 
                               data-from-addr="${trip.from_address || ''}" 
                               data-from-lat="${trip.from_lat || ''}" 
                               data-from-lng="${trip.from_lng || ''}" 
                               data-to="${trip.to}" 
                               data-to-addr="${trip.to_address || ''}" 
                               data-to-lat="${trip.to_lat || ''}" 
                               data-to-lng="${trip.to_lng || ''}">
                              <i class="bi bi-geo-alt-fill text-success"></i> Xem bản đồ
                            </a>
                          </div>
                        </div>
                      </td>
                      <td data-label="Dòng xe">
                        <div class="d-flex align-items-center gap-2">
                          <img src="${base}/assets/${typeInfo.img}" alt="${typeInfo.label}" class="rounded shadow-sm border" style="width: 55px; height: 38px; object-fit: cover; border-color: #e2ece7 !important; flex-shrink: 0;">
                          <div>
                            <span class="${typeInfo.badgeClass}">
                              <i class="bi ${typeInfo.icon} me-1"></i>${typeInfo.label}
                            </span>
                            <div class="mt-1"><small class="text-muted">${trip.bus_name}</small></div>
                          </div>
                        </div>
                      </td>
                      <td data-label="Trạng thái">
                        <div class="seat-indicator ${seatClass}">
                          <span class="seat-dot"></span>
                          <span>${seatLabel}</span>
                        </div>
                      </td>
                      <td data-label="Giá vé">
                        <strong class="fs-6 text-dark">${parseFloat(trip.price).toLocaleString('vi-VN')}đ</strong>
                      </td>
                      <td data-label="Hành động" class="text-end">
                        <button class="btn btn-book-now js-select-ticket" data-trip="${tripData}">
                          Chọn vé <i class="bi bi-plus-circle ms-1"></i>
                        </button>
                      </td>
                    </tr>
                  `;
                }).join('')}
              </tbody>
            </table>
            ${route.trips.length > 2 ? `
            <div class="text-center mt-3 pb-3 load-more-container">
              <button class="btn btn-outline-teal rounded-pill px-4 py-2 js-load-more" style="color: #0f766e; border-color: #0f766e; font-weight: 500;">
                <i class="bi bi-chevron-down me-1"></i> Xem thêm các chuyến khác
              </button>
            </div>
            ` : ''}
          </div>
        </div>
      `;
      
      const header = routeCard.querySelector('.route-group-header');
      header.addEventListener('click', () => {
        routeCard.classList.toggle('expanded');
      });

      // Add Load More functionality
      const loadMoreBtn = routeCard.querySelector('.js-load-more');
      if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', (e) => {
          e.stopPropagation(); // Ngăn sự kiện lan truyền
          const hiddenRows = routeCard.querySelectorAll('.timetable-row.d-none');
          for (let i = 0; i < 5 && i < hiddenRows.length; i++) {
            hiddenRows[i].classList.remove('d-none');
          }
          // Nếu không còn dòng nào ẩn thì ẩn nút Xem thêm
          if (routeCard.querySelectorAll('.timetable-row.d-none').length === 0) {
            loadMoreBtn.closest('.load-more-container').classList.add('d-none');
          }
        });
      }
      
      resultsContainer.appendChild(routeCard);
    });

    // Add Load More Routes button
    if (groups.length > 3) {
      const loadMoreContainer = document.createElement('div');
      loadMoreContainer.className = 'col-12 text-center mt-4 mb-3 load-more-routes-container';
      loadMoreContainer.innerHTML = `
        <button class="btn btn-primary rounded-pill px-5 py-2 shadow-sm js-load-more-routes" style="font-weight: 600; background-color: #0f766e; border-color: #0f766e;">
          <i class="bi bi-arrow-down-circle me-2"></i>Xem thêm tuyến đường
        </button>
      `;
      
      const loadMoreBtn = loadMoreContainer.querySelector('.js-load-more-routes');
      loadMoreBtn.addEventListener('click', () => {
        const hiddenRoutes = resultsContainer.querySelectorAll('.route-hidden');
        for (let i = 0; i < 3 && i < hiddenRoutes.length; i++) {
          hiddenRoutes[i].classList.remove('d-none', 'route-hidden');
        }
        if (resultsContainer.querySelectorAll('.route-hidden').length === 0) {
          loadMoreContainer.classList.add('d-none');
        }
      });
      
      resultsContainer.appendChild(loadMoreContainer);
    }
  }

  function renderEmptyState() {
    resultsContainer.innerHTML = `
      <div class="col-12 animated-item">
        <div class="empty-state-card">
          <i class="bi bi-calendar-x"></i>
          <h4>Không tìm thấy chuyến xe nào phù hợp</h4>
          <p class="text-muted mt-2">Quý khách vui lòng thử tìm kiếm hành trình khác hoặc điều chỉnh bộ lọc giờ chạy, dòng xe.</p>
          <button class="btn btn-success px-4 py-2 mt-2" id="clearFiltersBtn" style="background-color: #0f766e; border-color: #0f766e;">
            Xóa bộ lọc và xem lại
          </button>
        </div>
      </div>
    `;

    const clearBtn = document.getElementById('clearFiltersBtn');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        document.querySelectorAll('.time-pill-btn input[type="checkbox"], .type-pill-btn input[type="checkbox"]').forEach(c => {
          c.checked = false;
          c.closest('label').classList.remove('active');
        });
        activeTimeFilters = [];
        activeTypeFilters = [];
        const sortSelect = document.getElementById('sortBySelect');
        if (sortSelect) sortSelect.value = 'time-asc';
        currentSort = 'time-asc';
        
        applyFiltersAndRender();
      });
    }
  }

  /* SEARCH PAGE REDIRECT & DATE STRIP SLIDER DYNAMIC HELPERS */
  async function performSearch(from, to, date, returnDate, seats, tripType) {
    showLoadingState();
    
    // 1. Draw flight header
    renderFlightHeader(from, to, tripType);

    const filterCardWrapper = document.getElementById('filterCardWrapper');
    if (filterCardWrapper) filterCardWrapper.style.display = 'none';

    try {
      let tripsOut = [];
      let tripsReturn = [];
      let dateStripTrips = [];

      if (tripType === 'roundtrip') {
        const paramsOut = new URLSearchParams();
        paramsOut.set('from', from || '');
        paramsOut.set('to', to || '');
        paramsOut.set('date', date);
        paramsOut.set('limit', DATE_RESULT_LIMIT);
        if (seats) paramsOut.set('seats', seats);

        const paramsReturn = new URLSearchParams();
        paramsReturn.set('from', to || '');
        paramsReturn.set('to', from || '');
        
        // Return dates also centered around returnDate
        paramsReturn.set('date', returnDate || date);
        paramsReturn.set('limit', DATE_RESULT_LIMIT);
        if (seats) paramsReturn.set('seats', seats);

        const paramsStrip = new URLSearchParams();
        paramsStrip.set('from', from || '');
        paramsStrip.set('to', to || '');
        paramsStrip.set('date', addDays(date, -2));
        paramsStrip.set('limit', DATE_STRIP_LIMIT);

        const [respOut, respReturn, respStrip] = await Promise.all([
          fetch(`${base}/api/trips/search?${paramsOut.toString()}`),
          fetch(`${base}/api/trips/search?${paramsReturn.toString()}`),
          fetch(`${base}/api/trips/search?${paramsStrip.toString()}`)
        ]);
        const payloadOut = await respOut.json();
        const payloadReturn = await respReturn.json();
        const payloadStrip = await respStrip.json();

        tripsOut = payloadOut.data || [];
        tripsReturn = payloadReturn.data || [];
        dateStripTrips = payloadStrip.data || tripsOut;
      } else {
        const params = new URLSearchParams();
        params.set('from', from || '');
        params.set('to', to || '');
        params.set('date', date);
        params.set('limit', DATE_RESULT_LIMIT);
        if (seats) params.set('seats', seats);

        const paramsStrip = new URLSearchParams();
        paramsStrip.set('from', from || '');
        paramsStrip.set('to', to || '');
        paramsStrip.set('date', addDays(date, -2));
        paramsStrip.set('limit', DATE_STRIP_LIMIT);
        if (seats) paramsStrip.set('seats', seats);

        const [response, stripResponse] = await Promise.all([
          fetch(`${base}/api/trips/search?${params.toString()}`),
          fetch(`${base}/api/trips/search?${paramsStrip.toString()}`)
        ]);
        const payload = await response.json();
        const stripPayload = await stripResponse.json();
        
        tripsOut = payload.data || [];
        dateStripTrips = stripPayload.data || tripsOut;
        tripsReturn = [];
      }

      allOutboundTrips = tripsOut;
      allReturnTrips = tripsReturn;

      // 2. Render flight Date Carousel Slider based on the active selected date
      renderDateStrip(date, dateStripTrips);

      const outboundForActiveDate = tripsOut.filter(t => t.departure_time.startsWith(date));
      const returnForActiveDate = tripsReturn.filter(t => t.departure_time.startsWith(date));

      if (filterCardWrapper && (outboundForActiveDate.length > 0 || returnForActiveDate.length > 0)) {
        filterCardWrapper.style.display = '';
      }
      
      applyFiltersAndRender();
    } catch (error) {
      console.error('Lỗi khi tìm chuyến xe:', error);
      showErrorState();
    }
  }

  function renderDateStrip(selectedDate, tripsOut) {
    const wrapper = document.getElementById('dateStripWrapper');
    const container = document.getElementById('dateItemsRow');
    if (!wrapper || !container) return;

    wrapper.classList.remove('d-none');
    container.innerHTML = '';

    // Create 5 dates centered on the selected Date: D-2, D-1, D, D+1, D+2
    const dates = [];
    for (let i = -2; i <= 2; i++) {
      dates.push(addDays(selectedDate, i));
    }

    dates.forEach(dateStr => {
      const dateObj = new Date(dateStr);
      const dowIndex = dateObj.getDay();
      const dowNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
      const dowLabel = dowNames[dowIndex];
      
      const day = dateObj.getDate();
      const month = dateObj.getMonth() + 1;
      const dayMonthLabel = `${day} tháng ${month}`;

      // Calculate minimal starting price for this date
      const tripsOnDate = tripsOut.filter(t => t.departure_time.startsWith(dateStr));
      let priceLabel = 'K.Có Chuyến';
      if (tripsOnDate.length > 0) {
        const minPrice = Math.min(...tripsOnDate.map(t => parseFloat(t.price || 0)));
        priceLabel = `Từ ${minPrice.toLocaleString('vi-VN')} VND`;
      }

      const isActive = dateStr === selectedDate;
      const itemHtml = `
        <div class="date-item ${isActive ? 'active' : ''}" data-date="${dateStr}">
          <div class="date-item-dow">${dowLabel}</div>
          <div class="date-item-day">${dayMonthLabel}</div>
          <div class="date-item-price">${priceLabel}</div>
        </div>
      `;
      container.insertAdjacentHTML('beforeend', itemHtml);
    });

    // Add click listeners to Date Strip tabs
    container.querySelectorAll('.date-item').forEach(item => {
      item.addEventListener('click', () => {
        const clickedDate = item.getAttribute('data-date');
        document.getElementById('dateInput').value = clickedDate;
        
        // Update URL query string
        const newUrl = updateQueryStringParameter(window.location.href, 'date', clickedDate);
        window.history.pushState({ path: newUrl }, '', newUrl);

        // Fetch matching search
        const form = document.getElementById('tripSearchForm');
        if (form) {
          const formData = new FormData(form);
          const from = formData.get('from');
          const to = formData.get('to');
          const returnDate = formData.get('return_date');
          const seats = formData.get('seats');
          const tripType = formData.get('tripType') || 'oneway';
          performSearch(from, to, clickedDate, returnDate, seats, tripType);
        }
      });
    });
  }

  const airportCodesMap = {
    'hà nội': 'HAN',
    'tp. hồ chí minh': 'SGN',
    'hồ chí minh': 'SGN',
    'sài gòn': 'SGN',
    'đà nẵng': 'DAD',
    'nha trang': 'CXR',
    'đà lạt': 'DLI',
    'cần thơ': 'VCA',
    'sa pa': 'SQA',
    'lào cai': 'SQA',
    'hải phòng': 'HPH',
    'huế': 'HUI',
    'bình định': 'UIH',
    'quy nhơn': 'UIH',
    'vũng tàu': 'VTG',
    'phan thiết': 'PHT'
  };

  function getAirportCode(cityName) {
    const key = String(cityName || '').toLowerCase().trim();
    return airportCodesMap[key] || key.substring(0, 3).toUpperCase();
  }

  function renderFlightHeader(from, to, tripType) {
    const card = document.getElementById('searchHeaderCard');
    const originCity = document.getElementById('originCityName');
    const destCity = document.getElementById('destinationCityName');
    const badgeTripType = document.getElementById('badgeTripType');

    if (!card || !originCity || !destCity || !badgeTripType) return;

    if (from && to) {
      card.classList.remove('d-none');
      originCity.innerText = from;
      destCity.innerText = to;
      badgeTripType.innerText = tripType === 'roundtrip' ? 'Vé khứ hồi' : 'Vé một chiều';
    } else {
      card.classList.add('d-none');
    }
  }

  function addDays(dateStr, days) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return '';
    d.setDate(d.getDate() + days);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
  }

  function updateQueryStringParameter(uri, key, value) {
    const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    const separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
      return uri + separator + key + "=" + value;
    }
  }
})();
