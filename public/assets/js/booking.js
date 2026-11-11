(function () {
  document.addEventListener('submit', (event) => {
    const confirmForm = event.target.closest('#bookingConfirmForm');
    if (confirmForm) {
      const submitButton = confirmForm.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.dataset.originalText = submitButton.dataset.originalText || submitButton.textContent;
        submitButton.textContent = 'Dang xu ly...';
      }
    }
  });
})();
