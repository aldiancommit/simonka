/**
 * SIMONKA - Modern FundFlow Alert, Toast & Confirmation System
 * Fast, reactive, accessible, glassmorphism UI alerts compatible with Livewire SPA.
 */

(function () {
    'use strict';

    const SVG_ICONS = {
        success: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`,
        error: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`,
        danger: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`,
        warning: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
        info: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
        trash: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>`,
        close: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`
    };

    const TITLES = {
        success: 'Berhasil!',
        error: 'Terjadi Kesalahan!',
        danger: 'Terjadi Kesalahan!',
        warning: 'Perhatian!',
        info: 'Informasi'
    };

    /**
     * Get or create toast container
     */
    function getToastContainer() {
        let container = document.querySelector('.simonka-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'simonka-toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Show a modern floating toast notification
     */
    function showToast({ type = 'success', title = '', message = '', duration = 4500 }) {
        if (!message && !title) return;

        const container = getToastContainer();
        const normType = (type === 'danger' ? 'error' : type) || 'success';
        const displayTitle = title || TITLES[normType] || 'Notifikasi';
        const iconSvg = SVG_ICONS[normType] || SVG_ICONS.info;

        const toast = document.createElement('div');
        toast.className = `simonka-toast is-${normType}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');

        toast.innerHTML = `
            <div class="simonka-toast-icon-wrapper is-${normType}">
                ${iconSvg}
            </div>
            <div class="simonka-toast-body">
                <div class="simonka-toast-title">${escapeHtml(displayTitle)}</div>
                <div class="simonka-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="simonka-toast-close" aria-label="Tutup notifikasi">
                ${SVG_ICONS.close}
            </button>
            <div class="simonka-toast-progress"></div>
        `;

        container.appendChild(toast);

        const progressBar = toast.querySelector('.simonka-toast-progress');
        const closeBtn = toast.querySelector('.simonka-toast-close');

        let startTime = Date.now();
        let remaining = duration;
        let timer = null;
        let isPaused = false;

        function startTimer() {
            if (duration <= 0) return;
            startTime = Date.now();
            if (progressBar) {
                progressBar.style.transition = `width ${remaining}ms linear`;
                progressBar.style.width = '0%';
            }
            timer = setTimeout(() => {
                closeToast();
            }, remaining);
        }

        function pauseTimer() {
            if (duration <= 0 || isPaused) return;
            isPaused = true;
            clearTimeout(timer);
            remaining -= (Date.now() - startTime);
            if (remaining < 0) remaining = 0;
            if (progressBar) {
                const currentWidth = progressBar.getBoundingClientRect().width;
                const totalWidth = toast.getBoundingClientRect().width;
                const percent = (currentWidth / totalWidth) * 100;
                progressBar.style.transition = 'none';
                progressBar.style.width = `${percent}%`;
            }
        }

        function resumeTimer() {
            if (duration <= 0 || !isPaused) return;
            isPaused = false;
            startTimer();
        }

        function closeToast() {
            clearTimeout(timer);
            toast.classList.add('simonka-toast-hiding');
            toast.addEventListener('animationend', () => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, { once: true });
        }

        closeBtn.addEventListener('click', closeToast);
        toast.addEventListener('mouseenter', pauseTimer);
        toast.addEventListener('mouseleave', resumeTimer);

        // Start countdown
        startTimer();

        return {
            close: closeToast
        };
    }

    /**
     * Show a modern confirmation modal dialog
     */
    function showConfirm({
        title = 'Konfirmasi Tindakan',
        message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText = 'Ya, Lanjutkan',
        cancelText = 'Batal',
        type = 'danger'
    } = {}) {
        return new Promise((resolve) => {
            // Remove any existing confirm dialogs
            const oldBackdrop = document.querySelector('.simonka-confirm-backdrop');
            if (oldBackdrop) oldBackdrop.remove();

            const backdrop = document.createElement('div');
            backdrop.className = 'simonka-confirm-backdrop';
            
            const isDanger = type === 'danger' || type === 'error';
            const iconSvg = isDanger ? SVG_ICONS.trash : (type === 'warning' ? SVG_ICONS.warning : SVG_ICONS.info);
            const confirmBtnClass = isDanger ? 'btn-danger' : (type === 'primary' ? 'btn-fundflow-primary' : 'btn-fundflow-accent');

            backdrop.innerHTML = `
                <div class="simonka-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
                    <div class="simonka-confirm-icon-wrapper is-${isDanger ? 'danger' : type}">
                        ${iconSvg}
                    </div>
                    <h5 class="simonka-confirm-title" id="confirm-modal-title">${escapeHtml(title)}</h5>
                    <p class="simonka-confirm-message">${escapeHtml(message)}</p>
                    <div class="simonka-confirm-actions">
                        <button type="button" class="btn btn-fundflow-glass cancel-btn">${escapeHtml(cancelText)}</button>
                        <button type="button" class="btn ${confirmBtnClass} confirm-btn">
                            <span>${escapeHtml(confirmText)}</span>
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(backdrop);
            // Trigger animation frame
            requestAnimationFrame(() => {
                backdrop.classList.add('is-active');
            });

            const cancelBtn = backdrop.querySelector('.cancel-btn');
            const confirmBtn = backdrop.querySelector('.confirm-btn');

            function cleanup(result) {
                backdrop.classList.remove('is-active');
                document.removeEventListener('keydown', handleKey);
                setTimeout(() => {
                    if (backdrop.parentNode) {
                        backdrop.parentNode.removeChild(backdrop);
                    }
                    resolve(result);
                }, 250);
            }

            function handleKey(e) {
                if (e.key === 'Escape') {
                    cleanup(false);
                }
            }

            cancelBtn.addEventListener('click', () => cleanup(false));
            confirmBtn.addEventListener('click', () => {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5" role="status" aria-hidden="true"></span> Memproses...`;
                cleanup(true);
            });

            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    cleanup(false);
                }
            });

            document.addEventListener('keydown', handleKey);
            confirmBtn.focus();
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /**
     * Process flash messages from server rendered DOM
     */
    function processDomFlashMessages() {
        const flashElements = document.querySelectorAll('[data-simonka-flash]');
        flashElements.forEach((el) => {
            const type = el.getAttribute('data-flash-type') || 'success';
            const title = el.getAttribute('data-flash-title') || '';
            const message = el.getAttribute('data-flash-message') || el.textContent.trim();

            if (message) {
                showToast({ type, title, message });
            }
            el.remove(); // Consume once
        });
    }

    /**
     * Intercept delete / confirm forms
     */
    function bindConfirmationForms() {
        document.querySelectorAll('form').forEach((form) => {
            if (form.dataset.simonkaBound) return;

            const isDeleteForm = form.querySelector('input[name="_method"][value="DELETE"]') !== null;
            const hasConfirmAttr = form.hasAttribute('data-confirm') || form.hasAttribute('data-confirm-delete');

            if (isDeleteForm || hasConfirmAttr) {
                form.dataset.simonkaBound = 'true';

                // Remove legacy inline onsubmit if present
                if (form.getAttribute('onsubmit') && form.getAttribute('onsubmit').includes('confirm(')) {
                    form.removeAttribute('onsubmit');
                }

                form.addEventListener('submit', async function (e) {
                    if (form.dataset.confirmed === 'true') {
                        return; // proceed with native submit
                    }

                    e.preventDefault();

                    const title = form.getAttribute('data-confirm-title') || (isDeleteForm ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan');
                    const message = form.getAttribute('data-confirm-message') || form.getAttribute('data-confirm') || form.getAttribute('data-confirm-delete') || 
                        (isDeleteForm 
                            ? 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan data akan dihapus permanen dari sistem.'
                            : 'Apakah Anda yakin ingin melanjutkan tindakan ini?');
                    const confirmText = form.getAttribute('data-confirm-btn') || (isDeleteForm ? 'Ya, Hapus Data' : 'Ya, Lanjutkan');
                    const type = isDeleteForm ? 'danger' : (form.getAttribute('data-confirm-type') || 'primary');

                    const confirmed = await showConfirm({
                        title,
                        message,
                        confirmText,
                        cancelText: 'Batal',
                        type
                    });

                    if (confirmed) {
                        form.dataset.confirmed = 'true';
                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            }
        });
    }

    // Public API
    window.SimonkaAlert = {
        toast: showToast,
        success: (message, title) => showToast({ type: 'success', title: title || 'Berhasil!', message }),
        error: (message, title) => showToast({ type: 'error', title: title || 'Terjadi Kesalahan!', message }),
        warning: (message, title) => showToast({ type: 'warning', title: title || 'Perhatian!', message }),
        info: (message, title) => showToast({ type: 'info', title: title || 'Informasi', message }),
        confirm: showConfirm
    };

    // Initialize on DOM load and Livewire navigation
    function initialize() {
        processDomFlashMessages();
        bindConfirmationForms();
    }

    document.addEventListener('DOMContentLoaded', initialize);
    document.addEventListener('livewire:navigated', initialize);

})();
