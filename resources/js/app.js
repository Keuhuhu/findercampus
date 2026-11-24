import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ─── Page fade-in ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('page-ready');
});

/* ─── Scroll-reveal (IntersectionObserver) ─────────────── */
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

/* ─── Claim decision cards ─────────────────────────────── */
const claimActionGrid = document.querySelector('[data-claim-actions]');

if (claimActionGrid) {
    const decisionCards = [...claimActionGrid.querySelectorAll('[data-claim-card]')];
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!prefersReducedMotion && 'IntersectionObserver' in window) {
        claimActionGrid.classList.add('claim-decision-grid--animated');

        const decisionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    decisionObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.18 });

        decisionCards.forEach((card, index) => {
            card.style.setProperty('--claim-card-delay', `${index * 110}ms`);
            decisionObserver.observe(card);
        });
    } else {
        decisionCards.forEach(card => card.classList.add('is-visible'));
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-claim-form]');
    if (!form) return;

    const confirmationMessage = form.dataset.confirmMessage;
    if (confirmationMessage && !window.confirm(confirmationMessage)) {
        event.preventDefault();
        return;
    }

    const submitButton = form.querySelector('[data-claim-submit]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.setAttribute('aria-busy', 'true');
    }
});

/* ─── Direct report photo uploads ──────────────────────── */
document.querySelectorAll('[data-report-photo-uploader][data-direct-upload="true"]').forEach((uploader) => {
    const form = uploader.closest('form');
    const input = uploader.querySelector('[data-report-photo-input]');
    const hiddenList = uploader.querySelector('[data-upload-id-list]');
    const items = uploader.querySelector('[data-photo-upload-items]');
    const status = uploader.querySelector('[data-photo-upload-status]');
    const maxFiles = Number(uploader.dataset.maxFiles || 5);
    const initialExisting = Number(uploader.dataset.existingFiles || 0);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const pendingUploads = new Set();
    const failedUploads = new Set();

    if (!form || !input || !hiddenList || !items || !status || !csrfToken) return;

    const uploadCount = () => hiddenList.querySelectorAll('[data-upload-id]').length;
    const existingCount = () => Math.max(0, initialExisting - form.querySelectorAll('[data-remove-photo]:checked').length);
    const reportStatus = (message, isError = false) => {
        status.textContent = message;
        status.classList.toggle('text-red-600', isError);
        status.classList.toggle('text-gray-600', !isError);
    };

    input.addEventListener('change', async () => {
        const selectedFiles = [...input.files];
        if (existingCount() + uploadCount() + selectedFiles.length > maxFiles) {
            reportStatus(`Maksimal ${maxFiles} foto dapat disimpan pada laporan ini.`, true);
            input.value = '';
            return;
        }

        for (const file of selectedFiles) {
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
                reportStatus('Pilih gambar JPG, PNG, atau WEBP dengan ukuran maksimal 5 MB.', true);
                continue;
            }

            const row = document.createElement('div');
            row.className = 'flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs';
            const label = document.createElement('span');
            label.className = 'truncate pr-3 text-gray-700';
            label.textContent = `${file.name} sedang diunggah`;
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'shrink-0 font-semibold text-red-600 hover:text-red-800';
            removeButton.textContent = 'Batalkan';
            row.append(label, removeButton);
            items.append(row);
            pendingUploads.add(row);

            try {
                const signResponse = await fetch(uploader.dataset.signUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ content_type: file.type, size: file.size }),
                });

                if (!signResponse.ok) throw new Error('Tidak dapat menyiapkan unggahan foto.');

                const signedUpload = await signResponse.json();
                const uploadResponse = await fetch(signedUpload.url, {
                    method: 'PUT',
                    headers: signedUpload.headers,
                    body: file,
                });

                if (!uploadResponse.ok) throw new Error('Foto gagal diunggah ke penyimpanan. Coba lagi.');

                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'photo_upload_ids[]';
                hiddenId.value = signedUpload.id;
                hiddenId.dataset.uploadId = '';
                hiddenList.append(hiddenId);
                label.textContent = `${file.name} berhasil diunggah`;
                removeButton.textContent = 'Hapus';
                removeButton.addEventListener('click', async () => {
                    removeButton.disabled = true;
                    try {
                        const deleteUrl = uploader.dataset.deleteUrl.replace('UPLOAD_ID', encodeURIComponent(signedUpload.id));
                        const deleteResponse = await fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        });
                        if (!deleteResponse.ok) throw new Error('Foto sementara tidak dapat dihapus.');
                        hiddenId.remove();
                        row.remove();
                        reportStatus('Foto unggahan dihapus.');
                    } catch (error) {
                        removeButton.disabled = false;
                        reportStatus(error.message, true);
                    }
                });
                reportStatus(`${uploadCount()} foto siap disimpan.`);
            } catch (error) {
                failedUploads.add(row);
                label.textContent = `${file.name}: ${error.message}`;
                removeButton.textContent = 'Tutup';
                removeButton.addEventListener('click', () => {
                    failedUploads.delete(row);
                    row.remove();
                }, { once: true });
                reportStatus(error.message, true);
            } finally {
                pendingUploads.delete(row);
            }
        }

        input.value = '';
    });

    form.addEventListener('submit', (event) => {
        if (pendingUploads.size > 0 || failedUploads.size > 0) {
            event.preventDefault();
            reportStatus('Tunggu sampai unggahan foto selesai atau tutup foto yang gagal.', true);
        }
    });
});

// Re-run after Alpine finishes rendering
document.addEventListener('alpine:initialized', () => {
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
});

/* ─── Ripple effect on buttons ─────────────────────────── */
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-ripple');
    if (!btn) return;

    const existing = btn.querySelector('.ripple');
    if (existing) existing.remove();

    const circle = document.createElement('span');
    const diameter = Math.max(btn.clientWidth, btn.clientHeight);
    const radius = diameter / 2;
    const rect = btn.getBoundingClientRect();

    circle.classList.add('ripple');
    circle.style.cssText = `
        width: ${diameter}px;
        height: ${diameter}px;
        left: ${e.clientX - rect.left - radius}px;
        top: ${e.clientY - rect.top - radius}px;
    `;
    btn.appendChild(circle);
    setTimeout(() => circle.remove(), 600);
});

/* ─── Auto-dismiss flash messages ──────────────────────── */
document.querySelectorAll('[data-flash]').forEach(el => {
    // slide in
    requestAnimationFrame(() => el.classList.add('flash-show'));
    // slide out after 4s
    setTimeout(() => {
        el.classList.remove('flash-show');
        el.addEventListener('transitionend', () => el.remove(), { once: true });
    }, 4000);
});

/* ─── Smooth active nav indicator ──────────────────────── */
const currentPath = window.location.pathname;
document.querySelectorAll('[data-nav-link]').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
        link.setAttribute('data-active', 'true');
    }
});
