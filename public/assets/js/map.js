// Integrate Leaflet + OSRM for route preview with real road directions.
window.LobiBusMap = {
  leafletLoaded: false,
  mapInstance: null,

  // Lazy-load Leaflet CSS & JS from CDN on-demand
  loadLeaflet() {
    if (this.leafletLoaded) return Promise.resolve(true);

    return new Promise((resolve, reject) => {
      // 1. Append Leaflet CSS
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);

      // 2. Append Leaflet JS
      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      script.onload = () => {
        this.leafletLoaded = true;
        resolve(true);
      };
      script.onerror = () => reject(new Error('Lỗi khi tải thư viện bản đồ Leaflet từ CDN.'));
      document.body.appendChild(script);
    });
  },

  // Open modal and show route map
  async showMap(fromStation, fromAddress, fromLat, fromLng, toStation, toAddress, toLat, toLng) {
    // Fill text info in Left Panel of the Modal
    const fromStEl = document.getElementById('mapFromStation');
    const fromAddrEl = document.getElementById('mapFromAddress');
    const toStEl = document.getElementById('mapToStation');
    const toAddrEl = document.getElementById('mapToAddress');

    if (fromStEl) fromStEl.innerText = fromStation;
    if (fromAddrEl) fromAddrEl.innerText = fromAddress || 'Chưa cập nhật địa chỉ';
    if (toStEl) toStEl.innerText = toStation;
    if (toAddrEl) toAddrEl.innerText = toAddress || 'Chưa cập nhật địa chỉ';

    // Toggle Modal
    const modalEl = document.getElementById('mapModal');
    if (!modalEl) {
      console.error('Không tìm thấy #mapModal trong DOM.');
      return;
    }
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    // Lazy load Leaflet then render
    try {
      await this.loadLeaflet();
      setTimeout(() => {
        this.renderMap(fromLat, fromLng, toLat, toLng, fromStation, toStation);
      }, 350);
    } catch (e) {
      console.error('Lỗi khởi tạo bản đồ:', e);
      const container = document.getElementById('routeMap');
      if (container) {
        container.innerHTML = '<div class="p-4 text-center text-danger"><i class="bi bi-exclamation-octagon display-6 d-block mb-2"></i>Không thể tải bản đồ tại thời điểm này.</div>';
      }
    }
  },

  async renderMap(fromLat, fromLng, toLat, toLng, fromName, toName) {
    const container = document.getElementById('routeMap');
    if (!container) return;

    // Cleanup previous map instance
    if (this.mapInstance) {
      this.mapInstance.remove();
      this.mapInstance = null;
    }

    // Default coordinates fallback
    const latA = parseFloat(fromLat) || 21.0285;
    const lngA = parseFloat(fromLng) || 105.8542;
    const latB = parseFloat(toLat) || 18.6735;
    const lngB = parseFloat(toLng) || 105.6813;

    // Initialize Map
    this.mapInstance = L.map(container, { zoomControl: true });

    // OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(this.mapInstance);

    // Custom markers
    const greenIcon = L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    const redIcon = L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // Add markers
    const markerA = L.marker([latA, lngA], { icon: greenIcon })
      .addTo(this.mapInstance)
      .bindPopup('<b>Điểm đón:</b> ' + fromName);

    const markerB = L.marker([latB, lngB], { icon: redIcon })
      .addTo(this.mapInstance)
      .bindPopup('<b>Điểm trả:</b> ' + toName);

    // Fit bounds first so user sees something immediately
    const group = new L.featureGroup([markerA, markerB]);
    this.mapInstance.fitBounds(group.getBounds().pad(0.15));

    // Normalize endpoint order: always send the northernmost point (higher latitude) first.
    // This ensures A→B and B→A query OSRM with the same order → same route geometry.
    let queryLat1 = latA, queryLng1 = lngA, queryLat2 = latB, queryLng2 = lngB;
    let reversed = false;
    if (latA < latB || (latA === latB && lngA > lngB)) {
      queryLat1 = latB; queryLng1 = lngB;
      queryLat2 = latA; queryLng2 = lngA;
      reversed = true;
    }

    try {
      const osrmUrl = 'https://router.project-osrm.org/route/v1/driving/'
        + queryLng1 + ',' + queryLat1 + ';' + queryLng2 + ',' + queryLat2
        + '?overview=full&geometries=geojson&alternatives=false&continue_straight=true';

      const response = await fetch(osrmUrl);
      const data = await response.json();

      if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
        const route = data.routes[0];
        // GeoJSON is [lng, lat], Leaflet needs [lat, lng]
        let coords = route.geometry.coordinates.map(c => [c[1], c[0]]);

        // If the actual trip direction is reversed vs query order, flip the array
        if (reversed) coords = coords.reverse();

        // Simplify coordinates to remove small wiggles while keeping the overall shape
        const simplified = this.simplifyRoute(coords);

        // Draw the single road route with smooth rendering
        const routeLine = L.polyline(simplified, {
          color: '#0f766e',
          weight: 5,
          opacity: 0.85,
          smoothFactor: 2.5,
          lineCap: 'round',
          lineJoin: 'round'
        }).addTo(this.mapInstance);

        // Fit bounds to route for perfect framing
        this.mapInstance.fitBounds(routeLine.getBounds().pad(0.08));

        // Calculate distance and duration info
        const distKm = (route.distance / 1000).toFixed(1);
        const durMin = Math.round(route.duration / 60);
        const durH = Math.floor(durMin / 60);
        const durM = durMin % 60;
        const durText = durH > 0 ? durH + ' giờ ' + durM + ' phút' : durM + ' phút';

        // Show info popup at midpoint
        const mid = simplified[Math.floor(simplified.length / 2)];
        L.popup({ closeButton: false, className: 'route-info-popup' })
          .setLatLng(mid)
          .setContent(
            '<div style="text-align:center; font-family: Inter, sans-serif;">' +
            '<div style="font-weight:700; color:#0f766e; font-size:14px; margin-bottom:4px;">' +
            '<i class="bi bi-signpost-2"></i> ' + distKm + ' km</div>' +
            '<div style="color:#475569; font-size:12px;">' +
            '<i class="bi bi-clock"></i> ~' + durText + '</div></div>'
          )
          .addTo(this.mapInstance);

        // Open departure marker popup
        markerA.openPopup();
      } else {
        this.drawFallbackLine(latA, lngA, latB, lngB);
      }
    } catch (err) {
      console.warn('OSRM routing failed, using fallback straight line:', err);
      this.drawFallbackLine(latA, lngA, latB, lngB);
    }
  },

  /**
   * Simplify route coordinates using Douglas-Peucker algorithm
   * to remove small wiggles while preserving overall shape.
   * tolerance controls how aggressively to simplify (higher = smoother).
   */
  simplifyRoute(coords, tolerance) {
    if (!tolerance) tolerance = 0.0008; // ~80m tolerance for smooth highway look
    if (coords.length < 3) return coords;

    const sqDist = (p, a, b) => {
      let dx = b[0] - a[0], dy = b[1] - a[1];
      if (dx !== 0 || dy !== 0) {
        const t = Math.max(0, Math.min(1, ((p[0] - a[0]) * dx + (p[1] - a[1]) * dy) / (dx * dx + dy * dy)));
        dx = a[0] + t * dx - p[0];
        dy = a[1] + t * dy - p[1];
      } else {
        dx = p[0] - a[0];
        dy = p[1] - a[1];
      }
      return dx * dx + dy * dy;
    };

    const simplify = (pts, first, last, tol) => {
      let maxDist = 0, idx = 0;
      const tolSq = tol * tol;
      for (let i = first + 1; i < last; i++) {
        const d = sqDist(pts[i], pts[first], pts[last]);
        if (d > maxDist) { maxDist = d; idx = i; }
      }
      const result = [];
      if (maxDist > tolSq) {
        const left = simplify(pts, first, idx, tol);
        const right = simplify(pts, idx, last, tol);
        result.push(...left.slice(0, -1), ...right);
      } else {
        result.push(pts[first], pts[last]);
      }
      return result;
    };

    return simplify(coords, 0, coords.length - 1, tolerance);
  },

  drawFallbackLine(latA, lngA, latB, lngB) {
    L.polyline([[latA, lngA], [latB, lngB]], {
      color: '#0f766e',
      weight: 4,
      dashArray: '10, 8',
      opacity: 0.7
    }).addTo(this.mapInstance);
  }
};
