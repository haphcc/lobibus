(() => {
    const modal = document.querySelector('[data-admin-confirm-modal]');
    if (!modal) {
        return;
    }

    const message = modal.querySelector('[data-admin-confirm-message]');
    const cancelButton = modal.querySelector('[data-admin-confirm-cancel]');
    const confirmButton = modal.querySelector('[data-admin-confirm-submit]');
    let pendingForm = null;

    const close = () => {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        pendingForm = null;
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) {
            return;
        }

        if (form.dataset.confirmed === 'true') {
            form.dataset.confirmed = '';
            return;
        }

        event.preventDefault();
        pendingForm = form;
        message.textContent = form.dataset.confirm;
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        confirmButton.focus();
    });

    cancelButton.addEventListener('click', close);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            close();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('show')) {
            close();
        }
    });
    confirmButton.addEventListener('click', () => {
        if (!pendingForm) {
            close();
            return;
        }

        pendingForm.dataset.confirmed = 'true';
        pendingForm.requestSubmit();
    });
})();
