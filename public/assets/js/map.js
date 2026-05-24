// Integrate Leaflet/OpenStreetMap for route preview and station map.
window.LobiBusMap = {
  leafletLoaded: false,
  mapInstance: null,

  // Lazy-load Leaflet CSS & JS from CDN on-demand
  async loadLeaflet() {
    if (this.leafletLoaded) return true;
    
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

    // Lazy load Leaflet assets and draw
    try {
      await this.loadLeaflet();
      
      // A brief timeout allows the Bootstrap Modal fade transition to complete.
      // This is crucial to prevent Leaflet rendering bugs (gray/broken map tiles) caused by sizing changes.
      setTimeout(() => {
        this.renderMap(fromLat, fromLng, toLat, toLng, fromStation, toStation);
      }, 300);
    } catch (e) {
      console.error('Lỗi khởi tạo bản đồ:', e);
      const mapContainer = document.getElementById('leafletMap');
      if (mapContainer) {
        mapContainer.innerHTML = `<div class="p-4 text-center text-danger"><i class="bi bi-exclamation-octagon display-6 d-block mb-2"></i>Không thể tải bản đồ tại thời điểm này.</div>`;
      }
    }
  },

  renderMap(fromLat, fromLng, toLat, toLng, fromName, toName) {
    const container = document.getElementById('leafletMap');
    if (!container) return;
    
    // Cleanup previous map instance if any
    if (this.mapInstance) {
      this.mapInstance.remove();
      this.mapInstance = null;
    }

    // Default coordinates: Hanoi (departure) and Vinh (arrival) as clean fallbacks
    const latA = parseFloat(fromLat) || 21.0285;
    const lngA = parseFloat(fromLng) || 105.8542;
    const latB = parseFloat(toLat) || 18.6735;
    const lngB = parseFloat(toLng) || 105.6813;

    // Initialize Map centered dynamically
    this.mapInstance = L.map(container).setView([latA, lngA], 8);

    // OpenStreetMap standard tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    }).addTo(this.mapInstance);

    // Customized markers
    const greenIcon = L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    });

    const redIcon = L.icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    });

    // Add markers and bind popups
    const markerA = L.marker([latA, lngA], { icon: greenIcon })
      .addTo(this.mapInstance)
      .bindPopup(`<b>Điểm đón:</b> ${fromName}`)
      .openPopup();

    const markerB = L.marker([latB, lngB], { icon: redIcon })
      .addTo(this.mapInstance)
      .bindPopup(`<b>Điểm trả:</b> ${toName}`);

    // Draw route visual line (teal-green dashed line matching LobiBus brand)
    const latlngs = [[latA, lngA], [latB, lngB]];
    L.polyline(latlngs, { color: '#0f766e', weight: 4, dashArray: '8, 8' }).addTo(this.mapInstance);

    // Zoom the map bounds automatically to cover both departure & arrival markers
    const group = new L.featureGroup([markerA, markerB]);
    this.mapInstance.fitBounds(group.getBounds().pad(0.18));
  }
};
