import { prefersReducedMotion } from './motion';

/**
 * Page-wide effects that are not tied to one component:
 * scroll state of the navigation, scroll progress, the timeline line,
 * active-section links, scroll reveals, pointer light, image fallbacks and
 * the hero entrance sequence.
 */
export function initPageEffects() {
    const root = document.documentElement;
    const nav = document.querySelector('.nav');
    const progressBar = document.querySelector('[data-scroll-progress]');
    const timeline = document.querySelector('[data-timeline]');
    const timelineFill = document.querySelector('[data-timeline-fill]');

    let ticking = false;

    const onScroll = () => {
        const scrolled = window.scrollY;
        const scrollable = root.scrollHeight - window.innerHeight;

        nav?.classList.toggle('is-scrolled', scrolled > 12);

        if (progressBar) {
            progressBar.style.transform = `scaleX(${scrollable > 0 ? Math.min(1, scrolled / scrollable) : 0})`;
        }

        if (timeline && timelineFill) {
            const box = timeline.getBoundingClientRect();
            const progress = (window.innerHeight * 0.65 - box.top) / box.height;
            timelineFill.style.transform = `scaleY(${Math.max(0, Math.min(1, progress))})`;
        }

        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (! ticking) {
            ticking = true;
            requestAnimationFrame(onScroll);
        }
    }, { passive: true });

    trackActiveSection();
    revealOnScroll();
    followPointer();
    useImageFallbacks();
    playIntro();
    onScroll();
}

/** Underline the navigation link of the section in the middle of the viewport. */
function trackActiveSection() {
    const links = [...document.querySelectorAll('[data-nav]')];
    const setActive = (id) => {
        links.forEach((link) => {
            const active = link.dataset.nav === id;
            link.classList.toggle('is-active', active);

            if (active) {
                link.setAttribute('aria-current', 'true');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                setActive(entry.target.id === 'hero' ? null : entry.target.id);
            }
        });
    }, { rootMargin: '-45% 0px -50% 0px' });

    ['hero', ...new Set(links.map((link) => link.dataset.nav))]
        .map((id) => document.getElementById(id))
        .filter(Boolean)
        .forEach((section) => observer.observe(section));
}

function revealOnScroll() {
    document.querySelectorAll('.contact .wrap > [data-reveal]').forEach((element, index) => {
        element.style.setProperty('--d', `${index * 0.06}s`);
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    document.querySelectorAll('[data-reveal]').forEach((element) => observer.observe(element));
}

/** The soft light that follows the pointer on primary buttons and project cards. */
function followPointer() {
    if (! window.matchMedia('(hover: hover)').matches) {
        return;
    }

    document.addEventListener('pointermove', (event) => {
        const target = event.target.closest?.('.btn--primary, .card');

        if (! target) {
            return;
        }

        const box = target.getBoundingClientRect();
        target.style.setProperty('--mx', `${event.clientX - box.left}px`);
        target.style.setProperty('--my', `${event.clientY - box.top}px`);
    }, { passive: true });
}

/** Screenshots and the avatar sit on top of a placeholder; a broken image is simply removed. */
function useImageFallbacks() {
    document.querySelectorAll('img[data-fallback-image]').forEach((image) => {
        if (image.complete && image.naturalWidth === 0) {
            image.remove();
        } else {
            image.addEventListener('error', () => image.remove(), { once: true });
        }
    });
}

/** The one orchestrated moment: headline, copy and diagram stagger in, then the first trace runs. */
function playIntro() {
    document.querySelectorAll('.hero [data-intro]').forEach((element, index) => {
        element.style.setProperty('--d', `${0.35 + index * 0.1}s`);
    });

    requestAnimationFrame(() => {
        document.documentElement.classList.add('is-loaded');

        if (! prefersReducedMotion()) {
            window.dispatchEvent(new CustomEvent('hero-intro-finished-soon'));
        }
    });
}
