/**
 * Text Rotator - Animace měnícího se textu
 * Používá se v hero sekci pro rotaci textů
 */

class TextRotator {
    constructor(element) {
        this.element = element;
        this.texts = JSON.parse(element.dataset.texts || '[]');
        this.currentIndex = 0;
        this.isDeleting = false;
        this.currentText = '';
        this.typingSpeed = 100; // ms per character when typing
        this.deletingSpeed = 50; // ms per character when deleting
        this.pauseAfterComplete = 2000; // ms to pause after completing a word
        this.pauseBeforeDelete = 1500; // ms to pause before starting to delete

        if (this.texts.length > 0) {
            this.type();
        }
    }

    type() {
        // Support both array of strings and array of objects with 'text' property
        const textItem = this.texts[this.currentIndex];
        const fullText = typeof textItem === 'object' && textItem.text ? textItem.text : textItem;

        if (!this.isDeleting) {
            // Typing
            this.currentText = fullText.substring(0, this.currentText.length + 1);
            this.element.textContent = this.currentText;

            if (this.currentText === fullText) {
                // Finished typing this word
                setTimeout(() => {
                    this.isDeleting = true;
                    this.type();
                }, this.pauseAfterComplete);
                return;
            }

            setTimeout(() => this.type(), this.typingSpeed);
        } else {
            // Deleting
            this.currentText = fullText.substring(0, this.currentText.length - 1);
            // Použij non-breaking space když je text prázdný pro zachování výšky
            this.element.textContent = this.currentText || '\u00A0';

            if (this.currentText === '') {
                // Finished deleting
                this.isDeleting = false;
                this.currentIndex = (this.currentIndex + 1) % this.texts.length;
                setTimeout(() => this.type(), 500); // Short pause before next word
                return;
            }

            setTimeout(() => this.type(), this.deletingSpeed);
        }
    }
}

// Initialize all text rotators on page load
document.addEventListener('DOMContentLoaded', () => {
    const rotators = document.querySelectorAll('.rotating-text');
    rotators.forEach(element => {
        new TextRotator(element);
    });
});
