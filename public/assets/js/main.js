/**
 * Main JavaScript
 */

(function() {
    'use strict';

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }

    // Auto-hide flash messages
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 300);
        }, 5000);
    });

    // Lazy loading images (for browsers that don't support native lazy loading)
    if ('loading' in HTMLImageElement.prototype) {
        // Native lazy loading supported
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
            }
        });
    } else {
        // Fallback: IntersectionObserver
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazyload');
                        observer.unobserve(img);
                    }
                }
            });
        });

        const lazyImages = document.querySelectorAll('.lazyload');
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Form validation helper
    window.validateForm = function(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });

        return isValid;
    };

    // AJAX helper with CSRF token
    window.ajax = function(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (csrfToken && (options.method === 'POST' || options.method === 'PUT' || options.method === 'DELETE')) {
            defaults.headers['X-CSRF-Token'] = csrfToken.content;
        }

        const config = Object.assign({}, defaults, options);

        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                throw error;
            });
    };

    // Header visibility on scroll (hide on hero, show after)
    const header = document.querySelector('.site-header');
    const hero = document.querySelector('.hero');

    if (header && hero) {
        // Intersection Observer pro detekci kdy hero opustí viewport
        const heroObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Hero je viditelná - skrýt header
                        header.classList.remove('scrolled');
                    } else {
                        // Hero není viditelná - zobrazit header
                        header.classList.add('scrolled');
                    }
                });
            },
            {
                threshold: 0.1, // Trigger když je viditelných 10% hero sekce
                rootMargin: '0px'
            }
        );

        heroObserver.observe(hero);
    }

    console.log('Main JS loaded');
})();
