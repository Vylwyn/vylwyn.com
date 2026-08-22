/**
 * Scroll reveal.
 *
 * Elements carrying `.reveal` start hidden (see app.css) and fade up when they
 * enter the viewport. IntersectionObserver is used rather than a scroll
 * listener because the browser does the work off the main thread — a scroll
 * handler firing on every frame is a classic cause of janky phone scrolling.
 *
 * Each element is unobserved once revealed. Without that, the callback keeps
 * firing for the life of the page for no benefit.
 */
function initScrollReveal() {
    const targets = document.querySelectorAll('.reveal');

    if (targets.length === 0) {
        return;
    }

    // Honour the OS-level motion preference: show everything immediately.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        targets.forEach((el) => el.classList.add('is-visible'));

        return;
    }

    // Older browsers without IntersectionObserver get the content unanimated
    // rather than an invisible page.
    if (!('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            threshold: 0.1,
            // Trigger slightly before the element reaches the fold, so the
            // animation is already underway by the time it's fully visible.
            rootMargin: '0px 0px -50px',
        },
    );

    targets.forEach((el, index) => {
        // Stagger, capped at five steps so a long list doesn't crawl in.
        el.style.transitionDelay = `${Math.min(index, 5) * 55}ms`;
        observer.observe(el);
    });
}

document.addEventListener('DOMContentLoaded', initScrollReveal);

// Livewire swaps DOM on navigation; re-run so new nodes animate too.
document.addEventListener('livewire:navigated', initScrollReveal);
