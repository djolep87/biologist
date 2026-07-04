/**
 * Landing page interactions (welcome) — no Alpine dependency.
 */
export function initLandingPage() {
    const nav = document.getElementById('landing-nav');
    const hero = document.getElementById('pocetna');

    if (nav && hero) {
        const updateNav = () => {
            const threshold = Math.max(hero.offsetHeight - nav.offsetHeight, 80);
            nav.classList.toggle('landing-nav--light', window.scrollY >= threshold);
        };

        window.addEventListener('scroll', updateNav, { passive: true });
        window.addEventListener('resize', updateNav);
        updateNav();
    }

    const toggle = document.getElementById('landing-nav-toggle');
    const mobile = document.getElementById('landing-nav-mobile');

    if (toggle && mobile) {
        toggle.addEventListener('click', () => {
            const open = mobile.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        mobile.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobile.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    document.querySelectorAll('[data-faq-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = button.nextElementSibling;
            const icon = button.querySelector('[data-faq-icon]');
            const isOpen = panel?.classList.contains('is-open');

            document.querySelectorAll('[data-faq-panel]').forEach((p) => p.classList.remove('is-open'));
            document.querySelectorAll('[data-faq-toggle]').forEach((b) => {
                b.setAttribute('aria-expanded', 'false');
                b.querySelector('[data-faq-icon]')?.classList.remove('is-open');
            });

            if (!isOpen && panel) {
                panel.classList.add('is-open');
                button.setAttribute('aria-expanded', 'true');
                icon?.classList.add('is-open');
            }
        });
    });
}

if (document.getElementById('landing-nav')) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLandingPage);
    } else {
        initLandingPage();
    }
}
