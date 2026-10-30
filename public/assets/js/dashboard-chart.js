(function () {
  const palette = ['#0d9488', '#2563eb', '#f59e0b', '#dc2626', '#64748b', '#7c3aed', '#0891b2'];

  function numberValue(value) {
    return Number(value || 0);
  }

  function money(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')}đ`;
  }

  function chart(canvasId, configFactory) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;
    new Chart(canvas, configFactory(canvas));
  }

  function renderRevenueChart(data) {
    chart('revenueChart', () => ({
      type: 'bar',
      data: {
        labels: data.map((item) => item.label),
        datasets: [{
          label: 'Doanh thu',
          data: data.map((item) => numberValue(item.revenue)),
          backgroundColor: '#0d9488',
          borderRadius: 6,
        }],
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: (context) => money(context.raw) } },
        },
        scales: { y: { beginAtZero: true } },
      },
    }));
  }

  function renderDoughnut(canvasId, data, valueKey) {
    chart(canvasId, () => ({
      type: 'doughnut',
      data: {
        labels: data.map((item) => item.label),
        datasets: [{
          data: data.map((item) => numberValue(item[valueKey])),
          backgroundColor: palette,
          borderWidth: 0,
        }],
      },
      options: {
        responsive: true,
        cutout: '62%',
        plugins: { legend: { position: 'bottom' } },
      },
    }));
  }

  function renderBar(canvasId, data, valueKey, label) {
    chart(canvasId, () => ({
      type: 'bar',
      data: {
        labels: data.map((item) => item.label),
        datasets: [{
          label,
          data: data.map((item) => numberValue(item[valueKey])),
          backgroundColor: '#2563eb',
          borderRadius: 6,
        }],
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true } },
      },
    }));
  }

  window.LobiBusDashboard = {
    init() {
      const data = window.LobiBusStatisticData || {};
      renderRevenueChart(data.revenueByDay || []);
      renderDoughnut('bookingStatusChart', data.bookingStatusBreakdown || [], 'total');
      renderDoughnut('paymentMethodChart', data.paymentMethodBreakdown || [], 'total');
      renderDoughnut('tripStatusChart', data.tripStatusBreakdown || [], 'total');
      renderBar('userRoleChart', data.usersByRole || [], 'total', 'Người dùng');
    },
  };

  window.LobiBusDashboard.init();
})();
