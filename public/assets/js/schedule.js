(function () {
  const form = document.getElementById('tripSearchForm');
  const resultsContainer = document.getElementById('tripResults');
  const base = window.APP_BASE_URL || '';
  const DEFAULT_RESULT_LIMIT = 1500;
  const DATE_RESULT_LIMIT = 1500;
  const MAX_VISIBLE_ROUTES = 6;
  const MAX_TRIPS_PER_ROUTE = 5;
  
  // Active dynamic filters state
  let allTrips = []; // Raw trips retrieved from API
  let activeTimeFilters = []; // ['morning', 'afternoon', 'evening']
  let activeTypeFilters = []; // ['vip', 'sleeper', 'seat']
  let currentSort = 'time-asc'; // 'time-asc', 'time-desc', 'price-asc', 'price-desc'

  // Initialize
  document.addEventListener('DOMContentLoaded', () => {
    // 1. Set up swap button
    const swapBtn = document.getElementById('swapLocationsBtn');
    if (swapBtn) {
      swapBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const fromSelect = form.querySelector('select[name="from"]');
        const toSelect = form.querySelector('select[name="to"]');
        if (fromSelect && toSelect) {
          const temp = fromSelect.value;
          fromSelect.value = toSelect.value;
          toSelect.value = temp;
          // Trigger search automatically
          form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
      });
    }

    // 2. Set up dynamic filter checkboxes/pills
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

    // 3. Set up sorting
    const sortSelect = document.getElementById('sortBySelect');
    if (sortSelect) {
      sortSelect.addEventListener('change', (e) => {
        currentSort = e.target.value;
        applyFiltersAndRender();
      });
    }

    // 4. Auto-load schedules on page load
    if (form && form.dataset.autoLoad === '1') {
      loadInitialSchedules();
    }

    // 5. Setup Map Modal Click Trigger
    document.addEventListener('click', (e) => {
      const mapBtn = e.target.closest('.js-view-map');
      if (mapBtn) {
        e.preventDefault();
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
      }
    });
  });

  // Handle Form Search Submit
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const params = new URLSearchParams();
      
      const from = formData.get('from');
      const to = formData.get('to');
      const date = formData.get('date');

      if (from) params.set('from', from);
      if (to) params.set('to', to);
      if (date) {
        params.set('date', date);
        params.set('exact_date', '1');
      }
      params.set('limit', date ? DATE_RESULT_LIMIT : DEFAULT_RESULT_LIMIT);

      showLoadingState();

      try {
        const response = await fetch(`${base}/api/trips/search?${params.toString()}`);
        const payload = await response.json();
        allTrips = payload.data || [];
        applyFiltersAndRender();
        updateStatistics(allTrips);
      } catch (error) {
        console.error('Lỗi khi tải lịch trình:', error);
        showErrorState();
      }
    });
  }

  async function loadInitialSchedules() {
    showLoadingState();
    try {
      const params = new URLSearchParams();
      const formData = new FormData(form);
      const from = formData.get('from');
      const to = formData.get('to');
      const date = formData.get('date');

      if (from) params.set('from', from);
      if (to) params.set('to', to);
      if (date) {
        params.set('date', date);
        params.set('exact_date', '1');
      }
      params.set('limit', date ? DATE_RESULT_LIMIT : DEFAULT_RESULT_LIMIT);

      const response = await fetch(`${base}/api/trips/search?${params.toString()}`);
      const payload = await response.json();
      allTrips = payload.data || [];
      applyFiltersAndRender();
      updateStatistics(allTrips);
    } catch (error) {
      console.error('Lỗi khi tải lịch trình ban đầu:', error);
      showErrorState();
    }
  }

  // Update hero stats dynamically
  function updateStatistics(trips) {
    const routeSet = new Set();
    trips.forEach(t => routeSet.add(t.route_id));
    
    const countRoutes = document.getElementById('statRouteCount');
    const countTrips = document.getElementById('statTripCount');
    
    if (countRoutes) countRoutes.innerText = routeSet.size;
    if (countTrips) countTrips.innerText = trips.length;
  }

  function showLoadingState() {
    resultsContainer.innerHTML = `
      <div class="col-12 text-center py-5">
        <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="text-muted mt-3">Đang đồng bộ lịch trình chạy xe mới nhất từ LobiBus...</p>
      </div>
    `;
  }

  function showErrorState() {
    resultsContainer.innerHTML = `
      <div class="col-12 text-center py-5 text-danger">
        <i class="bi bi-exclamation-triangle-fill display-4"></i>
        <h4 class="mt-3">Không thể tải thông tin lịch trình</h4>
        <p class="text-muted">Đã xảy ra sự cố kết nối với hệ thống. Quý khách vui lòng thử lại sau.</p>
        <button class="btn btn-outline-danger mt-2" onclick="window.location.reload()">Tải lại trang</button>
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
    // Extract HH:MM from departure time (format: YYYY-MM-DD HH:MM:SS)
    const parts = timeStr.split(' ');
    if (parts.length < 2) return false;
    const timeOnly = parts[1];
    const hour = parseInt(timeOnly.split(':')[0], 10);

    if (range === 'morning') return hour >= 0 && hour < 12;
    if (range === 'afternoon') return hour >= 12 && hour < 18;
    if (range === 'evening') return hour >= 18 && hour < 24;
    return false;
  }

  // Apply filters client-side and render grouped routes
  function applyFiltersAndRender() {
    if (!allTrips.length) {
      renderEmptyState();
      return;
    }

    // 1. Filter flat trips
    let filteredTrips = allTrips.filter(trip => {
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

    // 2. Sort flat trips
    filteredTrips.sort((a, b) => {
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

    // 3. Group trips by Route ID
    const routeGroups = {};
    filteredTrips.forEach(trip => {
      const rId = trip.route_id || `${trip.from}-${trip.to}`;
      if (!routeGroups[rId]) {
        routeGroups[rId] = {
          route_id: trip.route_id,
          from: trip.from,
          to: trip.to,
          distance_km: trip.distance_km,
          duration_minutes: trip.duration_minutes,
          trips: [],
          minPrice: parseFloat(trip.price)
        };
      }
      routeGroups[rId].trips.push(trip);
      if (parseFloat(trip.price) < routeGroups[rId].minPrice) {
        routeGroups[rId].minPrice = parseFloat(trip.price);
      }
    });

    const routeList = Object.values(routeGroups);
    if (!routeList.length) {
      renderEmptyState();
      return;
    }

    routeList.sort((a, b) => {
      const aFirst = a.trips[0]?.departure_time || '';
      const bFirst = b.trips[0]?.departure_time || '';
      return new Date(aFirst) - new Date(bFirst);
    });

    routeList.forEach(route => {
      route.trips = route.trips.slice(0, MAX_TRIPS_PER_ROUTE);
      route.minPrice = Math.min(...route.trips.map(trip => parseFloat(trip.price || 0)));
    });

    renderRoutes(selectVisibleRoutes(routeList));
  }

  function selectVisibleRoutes(routeList) {
    const selectedDate = form ? new FormData(form).get('date') : '';
    if (!selectedDate || activeTimeFilters.length > 0) {
      return routeList.slice(0, MAX_VISIBLE_ROUTES);
    }

    const buckets = {
      morning: [],
      afternoon: [],
      evening: []
    };

    routeList.forEach(route => {
      const departure = route.trips[0]?.departure_time || '';
      const hour = parseInt((departure.split(' ')[1] || '00:00:00').split(':')[0], 10);
      if (hour < 12) {
        buckets.morning.push(route);
      } else if (hour < 18) {
        buckets.afternoon.push(route);
      } else {
        buckets.evening.push(route);
      }
    });

    const visible = [];
    ['morning', 'afternoon', 'evening'].forEach(bucket => {
      visible.push(...buckets[bucket].slice(0, 2));
    });

    if (visible.length < MAX_VISIBLE_ROUTES) {
      routeList.forEach(route => {
        if (visible.length < MAX_VISIBLE_ROUTES && !visible.includes(route)) {
          visible.push(route);
        }
      });
    }

    return visible;
  }

  function renderEmptyState() {
    resultsContainer.innerHTML = `
      <div class="col-12 animated-item">
        <div class="empty-state-card">
          <i class="bi bi-calendar-x"></i>
          <h4>Không tìm thấy lịch trình chạy xe phù hợp</h4>
          <p class="text-muted mt-2">Quý khách vui lòng điều chỉnh lại bộ lọc, đổi điểm đi/đến hoặc chọn ngày khởi hành khác.</p>
          <button class="btn btn-success px-4 py-2 mt-2" id="clearFiltersBtn">
            Xóa bộ lọc và xem tất cả
          </button>
        </div>
      </div>
    `;

    const clearBtn = document.getElementById('clearFiltersBtn');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        // Reset inputs and checkboxes
        document.querySelectorAll('.time-pill-btn input[type="checkbox"], .type-pill-btn input[type="checkbox"]').forEach(c => {
          c.checked = false;
          c.closest('label').classList.remove('active');
        });
        activeTimeFilters = [];
        activeTypeFilters = [];
        const sortSelect = document.getElementById('sortBySelect');
        if (sortSelect) sortSelect.value = 'time-asc';
        currentSort = 'time-asc';
        
        // Reset main search form inputs
        if (form) {
          form.querySelector('select[name="from"]').value = '';
          form.querySelector('select[name="to"]').value = '';
          form.querySelector('input[name="date"]').value = '';
        }
        
        loadInitialSchedules();
      });
    }
  }

  function renderRoutes(routes) {
    resultsContainer.innerHTML = '';
    
    routes.forEach((route, index) => {
      // Format Duration
      let durationStr = 'Đang cập nhật';
      if (route.duration_minutes) {
        const hours = Math.floor(route.duration_minutes / 60);
        const mins = route.duration_minutes % 60;
        durationStr = hours > 0 ? `${hours} giờ ${mins > 0 ? mins + ' phút' : ''}` : `${mins} phút`;
      }
      
      const distanceStr = route.distance_km ? `${route.distance_km} km` : 'Chưa xác định';
      const isExpanded = index === 0; // Expand the first route card by default for excellent UX
      
      const routeCard = document.createElement('div');
      routeCard.className = `col-12 route-group-card animated-item ${isExpanded ? 'expanded' : ''}`;
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
                <strong>${route.trips.length}</strong> chuyến gần nhất
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
                ${route.trips.map(trip => {
                  const typeInfo = getBusType(trip.bus_name);
                  
                  // Format time
                  const depDate = new Date(trip.departure_time);
                  const arrDate = new Date(trip.arrival_time);
                  
                  const depTimeStr = depDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                  const arrTimeStr = arrDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                  const depDateStr = depDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
                  
                  // Seat status colors
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

                  return `
                    <tr class="timetable-row">
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
                        <a href="${base}/booking/select-seat?trip_id=${trip.id}&return_url=${encodeURIComponent(`${window.location.pathname}${window.location.search}`)}" class="btn btn-book-now text-decoration-none d-inline-block">
                          Đặt vé <i class="bi bi-arrow-right-short ms-1"></i>
                        </a>
                      </td>
                    </tr>
                  `;
                }).join('')}
              </tbody>
            </table>
          </div>
        </div>
      `;
      
      // Add dynamic expand/collapse click event
      const header = routeCard.querySelector('.route-group-header');
      header.addEventListener('click', () => {
        routeCard.classList.toggle('expanded');
      });
      
      resultsContainer.appendChild(routeCard);
    });
  }
})();
