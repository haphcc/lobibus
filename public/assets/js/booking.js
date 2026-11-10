(function () {
  document.addEventListener('submit', (event) => {
    const cancelForm = event.target.closest('.js-cancel-booking-form');
    if (cancelForm && !window.confirm('Bạn chắc chắn muốn hủy vé này?')) {
      event.preventDefault();
      return;
    }

    const confirmForm = event.target.closest('#bookingConfirmForm');
    if (confirmForm && !window.confirm('Xác nhận tạo booking và giữ ghế?')) {
      event.preventDefault();
    }
  });
})();
