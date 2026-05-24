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

  // Khớp tên địa điểm đến với ảnh tương ứng
  function getDestinationImageFile(toName) {
    if (!toName) return '';
    let name = toName.toLowerCase().trim();
    
    // Loại bỏ dấu tiếng Việt để map với tên file
    const unicodeMap = {
      'a': 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
      'd': 'đ',
      'e': 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
      'i': 'í|ì|ỉ|ĩ|ị',
      'o': 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
      'u': 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
      'y': 'ý|ỳ|ỷ|ỹ|ỵ'
    };
    
    for (let char in unicodeMap) {
      const regex = new RegExp(unicodeMap[char], 'gi');
      name = name.replace(regex, char);
    }
    
    name = name.replace(/\s+/g, '');
    name = name.replace(/\./g, '');
    name = name.replace(/-/g, '');
    
    const specialMap = {
      'vinh': 'nghean.jpg',
      'tphcm': 'hochiminh.jpg',
      'saigon': 'hochiminh.jpg',
      'halong': 'haiphong.webp',
      'hatinh': 'nghean.jpg',
      'khanhhoa': 'nhatrang.jpg',
      'quangninh': 'haiphong.webp',
      'laichau': 'laocai.jpg',
      'dienbien': 'laocai.jpg',
      'sonla': 'yenbai.jpg',
      'langson': 'caobang.jpg',
      'tuyenquang': 'thainguyen.webp',
      'phutho': 'thainguyen.webp',
      'vinhlong': 'cantho.jpg',
      'angiang': 'kiengiang.jpg'
    };
    
    if (specialMap[name]) {
      return specialMap[name];
    }
    
    const extensions = {
      'haiphong': 'webp',
      'thanhhoa': 'webp',
      'tamky': 'webp',
      'backan': 'webp',
      'thainguyen': 'webp',
      'dongnai': 'webp',
      'namdinh': 'png',
      'camau': 'png',
      'binhdinh': 'jpeg'
    };
    
    const ext = extensions[name] || 'jpg';
    return `${name}.${ext}`;
  }

  function getTripAmenities(price) {
    const p = Number(price || 0);
    const amenitiesList = [
      { icon: 'bi-wifi', label: 'Wifi' },
      { icon: 'bi-usb-plug', label: 'Sạc USB' },
      { icon: 'bi-droplet-fill', label: 'Nước suối' },
      { icon: 'bi-snow', label: 'Điều hòa' }
    ];
    if (p > 300000) {
      return amenitiesList;
    } else if (p > 200000) {
      return [amenitiesList[0], amenitiesList[2], amenitiesList[3]];
    } else {
      return [amenitiesList[2], amenitiesList[3]];
    }
  }

  function renderSkeleton() {
    list.innerHTML = '';
    for (let i = 0; i < 4; i++) {
      const card = document.createElement('div');
      card.className = 'skeleton-card';
      card.innerHTML = `
        <div class="skeleton-header">
          <div class="skeleton-shimmer skeleton-badge"></div>
          <div class="skeleton-shimmer skeleton-price"></div>
        </div>
        <div class="skeleton-shimmer skeleton-title"></div>
        <div class="skeleton-shimmer skeleton-bus"></div>
        <div class="skeleton-meta">
          <div class="skeleton-timeline-point">
            <div class="skeleton-shimmer skeleton-icon"></div>
            <div class="skeleton-details">
              <div class="skeleton-shimmer skeleton-label"></div>
              <div class="skeleton-shimmer skeleton-time"></div>
            </div>
          </div>
          <div class="skeleton-timeline-point mt-3">
            <div class="skeleton-shimmer skeleton-icon"></div>
            <div class="skeleton-details">
              <div class="skeleton-shimmer skeleton-label"></div>
              <div class="skeleton-shimmer skeleton-time"></div>
            </div>
          </div>
          <div class="skeleton-shimmer skeleton-seats mt-3"></div>
        </div>
        <div class="skeleton-footer">
          <div class="skeleton-shimmer skeleton-left"></div>
          <div class="skeleton-shimmer skeleton-button"></div>
        </div>
      `;
      list.appendChild(card);
    }
  }

  function renderTrip(item) {
    const card = document.createElement('article');
    card.className = 'recommendation-card';
    
    // Add smooth fade-in transition on load
    card.style.opacity = '0';
    card.style.transform = 'translateY(12px)';
    card.style.transition = 'opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';

    const header = document.createElement('div');
    header.className = 'recommendation-card-header';
    
    // Build recommendation badge with custom icon and specific class
    const badge = document.createElement('span');
    let badgeClass = 'recommendation-badge';
    let iconHTML = '<i class="bi bi-stars me-1"></i>';
    
    if (item.reason === 'Chuyến rẻ nhất') {
      badgeClass += ' recommendation-badge-cheap';
      iconHTML = '<i class="bi bi-tag-fill me-1"></i>';
    } else if (item.reason === 'Khởi hành sớm nhất') {
      badgeClass += ' recommendation-badge-fast';
      iconHTML = '<i class="bi bi-clock-fill me-1"></i>';
    } else if (item.reason === 'Còn nhiều ghế nhất') {
      badgeClass += ' recommendation-badge-manyseats';
      iconHTML = '<i class="bi bi-person-fill-check me-1"></i>';
    } else if (item.reason === 'Phổ biến nhất') {
      badgeClass += ' recommendation-badge-popular';
      iconHTML = '<i class="bi bi-fire me-1"></i>';
    }
    
    badge.className = badgeClass;
    badge.innerHTML = iconHTML + (item.reason || 'Gợi ý');
    header.appendChild(badge);
    
    appendText(header, 'strong', formatMoney(item.price), 'recommendation-price');
    card.appendChild(header);

    // Chèn hình ảnh địa danh điểm đến
    const routeStr = item.route || '';
    let toName = '';
    if (routeStr.includes('->')) {
      toName = routeStr.split('->')[1].trim();
    } else if (routeStr.includes('→')) {
      toName = routeStr.split('→')[1].trim();
    } else if (routeStr.includes('-')) {
      toName = routeStr.split('-')[1].trim();
    }

    const imgFile = getDestinationImageFile(toName);
    if (imgFile) {
      const mediaDiv = document.createElement('div');
      mediaDiv.className = 'recommendation-card-media';
      const img = document.createElement('img');
      img.src = `${base}/assets/images/routes/${imgFile}`;
      img.alt = toName;
      img.loading = 'lazy';
      img.onerror = () => {
        mediaDiv.style.display = 'none';
      };
      mediaDiv.appendChild(img);
      card.appendChild(mediaDiv);
    }

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

    // Amenities Badges based on price or randomly selected for a premium realistic look
    const amenitiesDiv = document.createElement('div');
    amenitiesDiv.className = 'recommendation-amenities';
    
    const selectedAmenities = getTripAmenities(item.price);

    selectedAmenities.forEach(am => {
      const amBadge = document.createElement('span');
      amBadge.className = 'amenity-badge';
      amBadge.innerHTML = `<i class="bi ${am.icon}"></i>${am.label}`;
      amenitiesDiv.appendChild(amBadge);
    });
    card.appendChild(amenitiesDiv);

    const footer = document.createElement('div');
    footer.className = 'recommendation-card-footer';
    appendText(footer, 'span', `${Number(item.booking_count || 0)} lượt đặt`);

    const link = document.createElement('a');
    link.className = 'btn btn-success btn-sm';
    link.href = `${base}/booking/select-seat?trip_id=${encodeURIComponent(item.trip_id || '')}&return_url=${encodeURIComponent(`${window.location.pathname}${window.location.search}`)}`;
    link.textContent = 'Chọn ghế';
    footer.appendChild(link);
    card.appendChild(footer);

    // Trigger smooth fade-in after appending to DOM
    setTimeout(() => {
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 50);

    return card;
  }

  const price1 = document.getElementById('price1');
  const price2 = document.getElementById('price2');
  const price3 = document.getElementById('price3');
  const time1 = document.getElementById('time1');
  const time2 = document.getElementById('time2');
  const time3 = document.getElementById('time3');
  const amenityWifi = document.getElementById('amenityWifi');
  const amenityUsb = document.getElementById('amenityUsb');
  const amenityWater = document.getElementById('amenityWater');
  const amenityAc = document.getElementById('amenityAc');

  // Slider Elements
  const priceMinInput = document.getElementById('priceMinInput');
  const priceMaxInput = document.getElementById('priceMaxInput');
  const sliderTrack = document.getElementById('sliderTrack');
  const priceMinLabel = document.getElementById('priceMinLabel');
  const priceMaxLabel = document.getElementById('priceMaxLabel');
  const minGap = 20000; // Khoảng cách tối thiểu giữa 2 đầu là 20.000đ

  function getHour(dateTimeStr) {
    if (!dateTimeStr) return 0;
    const date = new Date(dateTimeStr.replace(' ', 'T'));
    if (Number.isNaN(date.getTime())) return 0;
    return date.getHours();
  }

  // Cập nhật vị trí thanh track xanh và nhãn hiển thị khoảng giá
  function updateSliderTrack() {
    if (!priceMinInput || !priceMaxInput || !sliderTrack || !priceMinLabel || !priceMaxLabel) return;

    let minVal = parseInt(priceMinInput.value);
    let maxVal = parseInt(priceMaxInput.value);
    const minLimit = parseInt(priceMinInput.min);
    const maxLimit = parseInt(priceMaxInput.max);

    // Ngăn chặn 2 nút trượt chồng lấn hoặc vượt qua nhau
    if (maxVal - minVal < minGap) {
      if (document.activeElement === priceMinInput) {
        priceMinInput.value = maxVal - minGap;
        minVal = parseInt(priceMinInput.value);
      } else {
        priceMaxInput.value = minVal + minGap;
        maxVal = parseInt(priceMaxInput.value);
      }
    }

    const range = maxLimit - minLimit;
    const minPercent = range > 0 ? ((minVal - minLimit) / range) * 100 : 0;
    const maxPercent = range > 0 ? ((maxVal - minLimit) / range) * 100 : 100;

    sliderTrack.style.left = minPercent + '%';
    sliderTrack.style.right = (100 - maxPercent) + '%';

    priceMinLabel.textContent = formatMoney(minVal);
    priceMaxLabel.textContent = formatMoney(maxVal);
  }

  // Khởi tạo giới hạn giá trị dựa trên chuyến đi thực tế để nút kéo trực quan
  function initPriceSliderLimits() {
    if (!priceMinInput || !priceMaxInput || trips.length === 0) return;

    const prices = trips.map(t => Number(t.price || 0));
    const minPrice = Math.min(...prices);
    const maxPrice = Math.max(...prices);

    // Làm tròn giới hạn về 50.000đ gần nhất để giao diện đẹp hơn
    const sliderMin = Math.max(0, Math.floor(minPrice / 50000) * 50000);
    const sliderMax = Math.ceil(maxPrice / 50000) * 50000;

    priceMinInput.min = sliderMin;
    priceMinInput.max = sliderMax;
    priceMaxInput.min = sliderMin;
    priceMaxInput.max = sliderMax;

    priceMinInput.value = sliderMin;
    priceMaxInput.value = sliderMax;

    updateSliderTrack();
  }

  // Khi kéo thanh trượt thủ công, bỏ chọn các checkbox nhanh
  function handleSliderInput() {
    [price1, price2, price3].forEach(cb => {
      if (cb) cb.checked = false;
    });
    updateSliderTrack();
    render();
  }

  // Khi nhấn vào checkbox nhanh (preset), đồng bộ thanh trượt nhảy về mốc tương ứng
  function handlePresetChange(e) {
    if (!priceMinInput || !priceMaxInput) return;
    
    if (!e.target.checked) {
      // Nếu bỏ check, khôi phục khoảng giá đầy đủ
      priceMinInput.value = priceMinInput.min;
      priceMaxInput.value = priceMaxInput.max;
      updateSliderTrack();
      render();
      return;
    }

    // Bỏ check các checkbox giá khác
    [price1, price2, price3].forEach(cb => {
      if (cb && cb !== e.target) cb.checked = false;
    });

    const sliderMin = parseInt(priceMinInput.min);
    const sliderMax = parseInt(priceMaxInput.max);

    if (e.target === price1) {
      priceMinInput.value = sliderMin;
      priceMaxInput.value = Math.min(200000, sliderMax);
    } else if (e.target === price2) {
      priceMinInput.value = Math.max(200000, sliderMin);
      priceMaxInput.value = Math.min(500000, sliderMax);
    } else if (e.target === price3) {
      priceMinInput.value = Math.max(500000, sliderMin);
      priceMaxInput.value = sliderMax;
    }

    updateSliderTrack();
    render();
  }

  function render() {
    let visibleTrips = activeFilter === 'all'
      ? trips
      : trips.filter((item) => item.reason === activeFilter);

    // Lọc theo khoảng giá của thanh trượt (nguồn chân lý duy nhất cho giá)
    if (priceMinInput && priceMaxInput) {
      const minP = Number(priceMinInput.value);
      const maxP = Number(priceMaxInput.value);
      visibleTrips = visibleTrips.filter((item) => {
        const p = Number(item.price || 0);
        return p >= minP && p <= maxP;
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

    // Filter by Amenities
    if (amenityWifi?.checked || amenityUsb?.checked || amenityWater?.checked || amenityAc?.checked) {
      visibleTrips = visibleTrips.filter((item) => {
        const tripAmLabels = getTripAmenities(item.price).map(am => am.label);
        if (amenityWifi?.checked && !tripAmLabels.includes('Wifi')) return false;
        if (amenityUsb?.checked && !tripAmLabels.includes('Sạc USB')) return false;
        if (amenityWater?.checked && !tripAmLabels.includes('Nước suối')) return false;
        if (amenityAc?.checked && !tripAmLabels.includes('Điều hòa')) return false;
        return true;
      });
    }

    list.innerHTML = '';

    if (visibleTrips.length === 0) {
      appendText(list, 'p', 'Không tìm thấy chuyến xe nào phù hợp với bộ lọc.', 'recommendation-empty');
      return;
    }

    visibleTrips.forEach((item) => list.appendChild(renderTrip(item)));
  }

  // Đăng ký sự kiện thay đổi cho các checkbox khác
  [time1, time2, time3, amenityWifi, amenityUsb, amenityWater, amenityAc].forEach(cb => {
    if (cb) cb.addEventListener('change', render);
  });

  // Đăng ký sự kiện đồng bộ cho các checkbox giá nhanh
  [price1, price2, price3].forEach(cb => {
    if (cb) cb.addEventListener('change', handlePresetChange);
  });

  // Đăng ký sự kiện kéo thả cho sliders
  if (priceMinInput) priceMinInput.addEventListener('input', handleSliderInput);
  if (priceMaxInput) priceMaxInput.addEventListener('input', handleSliderInput);

  filters.forEach((button) => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter || 'all';
      filters.forEach((filter) => filter.classList.remove('active'));
      button.classList.add('active');
      render();
    });
  });

  // Hiển thị trạng thái tải skeleton ban đầu
  renderSkeleton();
  updateSliderTrack();

  fetch(`${base}/api/recommendations`)
    .then((response) => response.json())
    .then((payload) => {
      trips = payload.data || [];
      initPriceSliderLimits(); // Khởi tạo khoảng trượt tối ưu từ dữ liệu thật
      render();
    })
    .catch(() => {
      list.innerHTML = '';
      appendText(list, 'p', 'Không tải được danh sách gợi ý.', 'recommendation-empty text-danger');
    });
})();
