/**
 * Hero Slideshow with Ken Burns Effect
 * Automatické přepínání obrázků v hero sekci s animacemi
 */

class HeroSlideshow {
    constructor(container, interval = 3000) {
        this.container = container;
        this.slides = container.querySelectorAll('.hero-slide');
        this.currentIndex = 0;
        this.interval = interval;
        this.timer = null;

        if (this.slides.length > 1) {
            this.start();
        }
    }

    start() {
        this.timer = setInterval(() => this.next(), this.interval);
    }

    stop() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    next() {
        // Aktuální slide - přidat fade-out class
        const currentSlide = this.slides[this.currentIndex];
        currentSlide.classList.add('fade-out');

        // Přejít na další slide (s wrap-around)
        this.currentIndex = (this.currentIndex + 1) % this.slides.length;

        // Nový slide - aktivovat (spustí Ken Burns + fade-in)
        const nextSlide = this.slides[this.currentIndex];
        nextSlide.classList.remove('fade-out'); // Vyčistit pokud tam zůstal
        nextSlide.classList.add('active');

        // Po dokončení fade-out vyčistit předchozí slide
        setTimeout(() => {
            currentSlide.classList.remove('active', 'fade-out');
        }, 800); // Délka fadeOut animace
    }

    goTo(index) {
        if (index >= 0 && index < this.slides.length) {
            this.slides[this.currentIndex].classList.remove('active');
            this.currentIndex = index;
            this.slides[this.currentIndex].classList.add('active');
        }
    }
}

// Inicializace při načtení stránky
document.addEventListener('DOMContentLoaded', () => {
    const slideshowContainer = document.querySelector('.hero-slideshow-container');

    if (slideshowContainer) {
        // Spustit slideshow s intervalem 3000ms (3s)
        const slideshow = new HeroSlideshow(slideshowContainer, 3000);

        // Opcionalní: zastavit slideshow při hover nad hero sekcí
        const heroSection = document.querySelector('.hero-slideshow');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', () => slideshow.stop());
            heroSection.addEventListener('mouseleave', () => slideshow.start());
        }

        // Progressive loading dalších obrázků na pozadí
        const slides = slideshowContainer.querySelectorAll('.hero-slide');
        slides.forEach((slide, index) => {
            if (index > 0) {
                const bgUrl = slide.style.backgroundImage.match(/url\(['"]?([^'"]+)['"]?\)/)?.[1];
                if (bgUrl) {
                    const img = new Image();
                    img.src = bgUrl;
                }
            }
        });

        console.log('Hero slideshow initialized:', slides.length, 'slides');
    }
});
