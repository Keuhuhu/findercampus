import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ─── Shared motion and scroll controls ────────────────── */
const sitePrefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
let revealObserver = null;

const prepareMotionTargets = () => {
    const targets = document.querySelectorAll('.reveal, .card-lift, main section, main article, .guest-content > div');

    targets.forEach((target, index) => {
        if (target.dataset.motionPrepared === 'true') return;

        target.dataset.motionPrepared = 'true';
        target.classList.add('motion-reveal-target');
        target.style.setProperty('--motion-delay', `${Math.min(index % 5, 4) * 55}ms`);

        if (revealObserver) {
            revealObserver.observe(target);
        } else {
            target.classList.add('revealed');
        }
    });
};

if (!sitePrefersReducedMotion && 'IntersectionObserver' in window) {
    document.body.classList.add('motion-enabled');
    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -48px 0px' });
}

prepareMotionTargets();

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('page-ready');
});

const scrollProgress = document.createElement('div');
scrollProgress.className = 'scroll-progress';
scrollProgress.setAttribute('aria-hidden', 'true');
scrollProgress.innerHTML = '<span></span>';
document.body.append(scrollProgress);

const backToTop = document.createElement('button');
backToTop.className = 'back-to-top';
backToTop.type = 'button';
backToTop.setAttribute('aria-label', 'Kembali ke atas');
backToTop.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 14 6-6 6 6M12 9v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
document.body.append(backToTop);

let scrollUpdateQueued = false;
const updateScrollControls = () => {
    const scrollableDistance = document.documentElement.scrollHeight - window.innerHeight;
    const progress = scrollableDistance > 0 ? Math.min(1, Math.max(0, window.scrollY / scrollableDistance)) : 0;

    document.documentElement.style.setProperty('--page-scroll-progress', String(progress));
    document.body.classList.toggle('has-scrolled', window.scrollY > 24);
    backToTop.classList.toggle('back-to-top--visible', window.scrollY > 480);
    backToTop.tabIndex = window.scrollY > 480 ? 0 : -1;
    scrollUpdateQueued = false;
};

window.addEventListener('scroll', () => {
    if (scrollUpdateQueued) return;

    scrollUpdateQueued = true;
    window.requestAnimationFrame(updateScrollControls);
}, { passive: true });

window.addEventListener('resize', updateScrollControls, { passive: true });
updateScrollControls();

backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: sitePrefersReducedMotion ? 'auto' : 'smooth' });
});

if (!sitePrefersReducedMotion && 'IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const element = entry.target;
            const finalValue = Number(element.textContent.replace(/[^\d.-]/g, ''));
            if (!Number.isFinite(finalValue) || finalValue <= 0) {
                observer.unobserve(element);
                return;
            }

            const startTime = performance.now();
            const duration = 900;
            const animateCounter = (now) => {
                const progress = Math.min(1, (now - startTime) / duration);
                const easedProgress = 1 - Math.pow(1 - progress, 3);
                element.textContent = new Intl.NumberFormat('id-ID').format(Math.round(finalValue * easedProgress));

                if (progress < 1) {
                    window.requestAnimationFrame(animateCounter);
                }
            };

            window.requestAnimationFrame(animateCounter);
            observer.unobserve(element);
        });
    }, { threshold: 0.55 });

    document.querySelectorAll('[data-count-up]').forEach((element) => counterObserver.observe(element));

    const weightBarObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('weight-bar--visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.smart-weight-bar').forEach((bar) => weightBarObserver.observe(bar));
}

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
const REPORT_PHOTO_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const REPORT_PHOTO_SOURCE_MAX_BYTES = 30 * 1024 * 1024;
const REPORT_PHOTO_MAX_DIMENSION = 2400;

const formatPhotoSize = (bytes) => (bytes / (1024 * 1024)).toFixed(1) + ' MB';

const optimizeReportPhoto = async (file, maxBytes) => {
    if (!REPORT_PHOTO_MIME_TYPES.includes(file.type)) {
        throw new Error('Pilih gambar JPG, PNG, atau WEBP.');
    }

    if (file.size > REPORT_PHOTO_SOURCE_MAX_BYTES) {
        throw new Error('Ukuran foto asli maksimal 30 MB.');
    }

    if (typeof createImageBitmap !== 'function') {
        if (file.size <= maxBytes) return file;
        throw new Error('Browser ini tidak mendukung optimasi foto. Pilih foto di bawah 10 MB.');
    }

    let bitmap;

    try {
        bitmap = await createImageBitmap(file);
    } catch {
        if (file.size <= maxBytes) return file;
        throw new Error('Foto tidak dapat dioptimasi. Pilih foto lain dengan ukuran di bawah 10 MB.');
    }

    try {
        const longestSide = Math.max(bitmap.width, bitmap.height);
        const needsOptimization = file.size > 3 * 1024 * 1024 || longestSide > REPORT_PHOTO_MAX_DIMENSION;

        if (!needsOptimization) return file;

        const outputType = file.type === 'image/png' ? 'image/webp' : file.type;
        const attempts = [
            { maxDimension: REPORT_PHOTO_MAX_DIMENSION, quality: 0.9 },
            { maxDimension: 2000, quality: 0.84 },
            { maxDimension: 1600, quality: 0.76 },
        ];
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');

        if (!context) {
            if (file.size <= maxBytes) return file;
            throw new Error('Foto tidak dapat dioptimasi di browser ini.');
        }

        for (const attempt of attempts) {
            const scale = Math.min(1, attempt.maxDimension / longestSide);
            canvas.width = Math.max(1, Math.round(bitmap.width * scale));
            canvas.height = Math.max(1, Math.round(bitmap.height * scale));
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);

            const blob = await new Promise((resolve) => {
                canvas.toBlob(resolve, outputType, attempt.quality);
            });

            if (!blob || !REPORT_PHOTO_MIME_TYPES.includes(blob.type)) continue;
            if (blob.size > maxBytes) continue;
            if (blob.size >= file.size && file.size <= maxBytes) return file;

            const extensionByMime = {
                'image/jpeg': 'jpg',
                'image/png': 'png',
                'image/webp': 'webp',
            };
            const baseName = file.name.replace(/\.[^.]+$/, '');
            const optimizedName = baseName + '.' + extensionByMime[blob.type];

            return new File([blob], optimizedName, {
                type: blob.type,
                lastModified: file.lastModified,
            });
        }

        if (file.size <= maxBytes) return file;

        throw new Error('Foto tetap lebih besar dari 10 MB setelah dioptimasi. Pilih foto lain.');
    } finally {
        bitmap.close();
    }
};

document.querySelectorAll('[data-report-photo-uploader][data-direct-upload="true"]').forEach((uploader) => {
    const form = uploader.closest('form');
    const input = uploader.querySelector('[data-report-photo-input]');
    const hiddenList = uploader.querySelector('[data-upload-id-list]');
    const items = uploader.querySelector('[data-photo-upload-items]');
    const status = uploader.querySelector('[data-photo-upload-status]');
    const previews = uploader.parentElement.querySelector('[data-photo-preview-grid]');
    const maxFiles = Number(uploader.dataset.maxFiles || 5);
    const maxFileBytes = Number(uploader.dataset.maxFileBytes || 10 * 1024 * 1024);
    const initialExisting = Number(uploader.dataset.existingFiles || 0);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const pendingUploads = new Set();
    const failedUploads = new Set();
    const previewUrls = new Map();
    let dismissedFailedUpload = false;

    if (!form || !input || !hiddenList || !items || !status || !previews || !csrfToken) return;

    const uploadCount = () => hiddenList.querySelectorAll('[data-upload-id]').length;
    const existingCount = () => Math.max(0, initialExisting - form.querySelectorAll('[data-remove-photo]:checked').length);
    const reportStatus = (message, isError = false) => {
        status.textContent = message;
        status.classList.toggle('text-red-600', isError);
        status.classList.toggle('text-gray-600', !isError);
    };
    const refreshStatus = () => {
        if (pendingUploads.size > 0) {
            reportStatus(`${pendingUploads.size} foto sedang diunggah.`);
        } else if (failedUploads.size > 0) {
            reportStatus(`${failedUploads.size} foto gagal. Tutup foto yang gagal untuk membuangnya.`, true);
        } else if (uploadCount() > 0) {
            reportStatus(`${uploadCount()} foto siap disimpan.`);
        } else if (dismissedFailedUpload) {
            reportStatus('Foto gagal dibuang. Laporan dapat dilanjutkan tanpa foto tersebut.');
        } else {
            reportStatus('');
        }
    };
    const createPreview = (file, previewId) => {
        const preview = document.createElement('div');
        preview.className = 'relative aspect-square overflow-hidden rounded-lg border border-gray-200';
        preview.dataset.previewId = previewId;

        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.alt = file.name;
        image.className = 'h-full w-full object-cover';
        previewUrls.set(previewId, image.src);
        preview.append(image);

        previews.append(preview);
        previews.classList.remove('hidden');
        previews.classList.add('grid');
    };
    const syncPrimaryPreview = () => {
        previews.querySelectorAll('[data-primary-badge]').forEach((badge) => badge.remove());

        if (existingCount() > 0) return;

        const firstUpload = hiddenList.querySelector('[data-upload-id]');
        const previewId = firstUpload?.dataset.previewId;
        if (!previewId) return;

        const preview = previews.querySelector(`[data-preview-id="${previewId}"]`);
        if (!preview) return;

        const badge = document.createElement('div');
        badge.className = 'absolute inset-x-0 top-0 bg-primary/80 py-1 text-center text-[10px] font-bold text-white backdrop-blur-sm';
        badge.dataset.primaryBadge = '';
        badge.textContent = 'FOTO UTAMA';
        preview.append(badge);
    };
    const removePreview = (previewId) => {
        const previewUrl = previewUrls.get(previewId);
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrls.delete(previewId);
        }

        previews.querySelector(`[data-preview-id="${previewId}"]`)?.remove();
        if (previews.childElementCount === 0) {
            previews.classList.remove('grid');
            previews.classList.add('hidden');
        }
    };

    input.addEventListener('change', async () => {
        const selectedFiles = [...input.files];
        if (existingCount() + uploadCount() + selectedFiles.length > maxFiles) {
            reportStatus(`Maksimal ${maxFiles} foto dapat disimpan pada laporan ini.`, true);
            input.value = '';
            return;
        }

        for (const file of selectedFiles) {
            if (!REPORT_PHOTO_MIME_TYPES.includes(file.type) || file.size > REPORT_PHOTO_SOURCE_MAX_BYTES) {
                reportStatus('Pilih gambar JPG, PNG, atau WEBP dengan ukuran foto asli maksimal 30 MB.', true);
                continue;
            }

            const row = document.createElement('div');
            const previewId = window.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`;
            row.dataset.previewId = previewId;
            row.className = 'flex items-center justify-between rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs';
            row.addEventListener('click', (event) => event.stopPropagation());
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
            refreshStatus();

            try {
                const uploadFile = await optimizeReportPhoto(file, maxFileBytes);
                const optimizationNotice = uploadFile.size < file.size
                    ? ' (dioptimasi dari ' + formatPhotoSize(file.size) + ' menjadi ' + formatPhotoSize(uploadFile.size) + ')'
                    : '';
                createPreview(uploadFile, previewId);
                label.textContent = file.name + ' sedang diunggah' + optimizationNotice;

                const signResponse = await fetch(uploader.dataset.signUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ content_type: uploadFile.type, size: uploadFile.size }),
                });

                if (!signResponse.ok) throw new Error('Tidak dapat menyiapkan unggahan foto.');

                const signedUpload = await signResponse.json();
                const uploadResponse = await fetch(signedUpload.url, {
                    method: 'PUT',
                    headers: signedUpload.headers,
                    body: uploadFile,
                });

                if (!uploadResponse.ok) throw new Error('Foto gagal diunggah ke penyimpanan. Coba lagi.');

                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'photo_upload_ids[]';
                hiddenId.value = signedUpload.id;
                hiddenId.dataset.uploadId = '';
                hiddenId.dataset.previewId = previewId;
                hiddenList.append(hiddenId);
                syncPrimaryPreview();
                label.textContent = `${file.name} berhasil diunggah`;
                removeButton.textContent = 'Hapus';
                removeButton.addEventListener('click', async (event) => {
                    event.stopPropagation();
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
                        removePreview(previewId);
                        syncPrimaryPreview();
                        refreshStatus();
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
                removeButton.title = 'Tutup dan buang foto yang gagal';
                removeButton.setAttribute('aria-label', `Tutup dan buang foto ${file.name} yang gagal`);
                removeButton.addEventListener('click', (event) => {
                    event.stopPropagation();
                    failedUploads.delete(row);
                    removePreview(previewId);
                    row.remove();
                    dismissedFailedUpload = true;
                    refreshStatus();
                }, { once: true });
            } finally {
                pendingUploads.delete(row);
                refreshStatus();
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
document.querySelectorAll('[data-report-gallery]').forEach((gallery) => {
    const mainImage = gallery.querySelector('[data-gallery-main]');
    const thumbnails = [...gallery.querySelectorAll('[data-gallery-thumbnail]')];
    const counter = gallery.querySelector('[data-gallery-counter]');

    if (!mainImage || thumbnails.length < 2) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let activeIndex = Math.max(0, thumbnails.findIndex((button) => button.getAttribute('aria-pressed') === 'true'));
    let selectionRequest = 0;

    const setActivePhoto = (index) => {
        activeIndex = index;
        thumbnails.forEach((button, buttonIndex) => {
            button.setAttribute('aria-pressed', buttonIndex === index ? 'true' : 'false');
        });
        mainImage.alt = thumbnails[index].dataset.imageAlt || mainImage.alt;

        if (counter) {
            counter.textContent = (index + 1) + ' dari ' + thumbnails.length + ' foto';
        }
    };

    const showPhoto = (index) => {
        const request = ++selectionRequest;

        if (index === activeIndex) {
            mainImage.classList.remove('report-gallery__main-image--switching');
            return;
        }

        const thumbnail = thumbnails[index];
        const nextSource = thumbnail.dataset.imageUrl;
        const preloadedImage = new Image();
        let hasLoaded = false;

        const switchImage = () => {
            if (hasLoaded || request !== selectionRequest) return;
            hasLoaded = true;
            mainImage.classList.add('report-gallery__main-image--switching');

            const updateImage = () => {
                if (request !== selectionRequest) return;

                mainImage.src = nextSource;
                setActivePhoto(index);
                window.requestAnimationFrame(() => {
                    mainImage.classList.remove('report-gallery__main-image--switching');
                });
            };

            if (prefersReducedMotion) {
                updateImage();
            } else {
                window.setTimeout(updateImage, 110);
            }
        };

        preloadedImage.onload = switchImage;
        preloadedImage.onerror = () => {
            if (request === selectionRequest) {
                mainImage.classList.remove('report-gallery__main-image--switching');
            }
        };
        preloadedImage.src = nextSource;

        if (preloadedImage.complete && preloadedImage.naturalWidth > 0) {
            switchImage();
        }
    };

    thumbnails.forEach((button, index) => {
        button.addEventListener('click', () => showPhoto(index));

        if (!prefersReducedMotion) {
            button.style.setProperty('--gallery-delay', (index * 55) + 'ms');
        }
    });

    gallery.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;

        event.preventDefault();
        const direction = event.key === 'ArrowRight' ? 1 : -1;
        const nextIndex = (activeIndex + direction + thumbnails.length) % thumbnails.length;
        showPhoto(nextIndex);
    });

    setActivePhoto(activeIndex);

    if (!prefersReducedMotion) {
        gallery.classList.add('report-gallery--enhanced');
    }
});

document.addEventListener('alpine:initialized', () => {
    prepareMotionTargets();
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
