/**
 * Mobile Menu Toggle
 * Hamburger menu pro mobilní zařízení
 */

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.querySelector('.main-nav');

    if (!toggle || !nav) {
        return; // Elements not found
    }

    toggle.addEventListener('click', () => {
        // Toggle active class
        toggle.classList.toggle('active');
        nav.classList.toggle('active');

        // Update aria-expanded attribute for accessibility
        const isExpanded = toggle.classList.contains('active');
        toggle.setAttribute('aria-expanded', isExpanded.toString());

        // Prevent body scroll when menu is open on mobile
        if (isExpanded) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!toggle.contains(e.target) && !nav.contains(e.target)) {
            toggle.classList.remove('active');
            nav.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    });

    // Close menu when clicking on a link (smooth navigation)
    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            toggle.classList.remove('active');
            nav.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        });
    });

    // Close menu on window resize if going from mobile to desktop
    let previousWidth = window.innerWidth;
    window.addEventListener('resize', () => {
        const currentWidth = window.innerWidth;

        // If resizing from mobile to desktop (crossing 768px threshold)
        if (previousWidth <= 768 && currentWidth > 768) {
            toggle.classList.remove('active');
            nav.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        previousWidth = currentWidth;
    });
});
