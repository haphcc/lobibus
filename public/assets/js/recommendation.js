(function () {
  const list = document.getElementById('recommendationList');
  const base = window.APP_BASE_URL || '';
  if (!list) return;

  fetch(`${base}/api/recommendations`)
    .then((response) => response.json())
    .then((payload) => {
      list.innerHTML = (payload.data || []).map((item) => `
        <div class="col-md-4">
          <div class="card card-body h-100">
            <h5>${item.route}</h5>
            <p>${item.reason || ''}</p>
            <strong>${item.price ? Number(item.price).toLocaleString('vi-VN') + ' VND' : ''}</strong>
          </div>
        </div>
      `).join('');
    });
})();
