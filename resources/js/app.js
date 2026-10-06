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
