/**
 * Hero Video Autoplay
 * Explicitní spuštění videa pro zajištění autoplay na všech platformách
 */

document.addEventListener('DOMContentLoaded', () => {
    const videoElement = document.querySelector('.hero-video-element');

    if (videoElement) {
        // Pokus o spuštění videa
        const playPromise = videoElement.play();

        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    console.log('Hero video autoplay started successfully');
                })
                .catch(error => {
                    console.warn('Hero video autoplay failed:', error);

                    // Fallback: Pokus o spuštění při prvním user interaction
                    const startVideoOnInteraction = () => {
                        videoElement.play()
                            .then(() => {
                                console.log('Hero video started after user interaction');
                                // Odstranit event listenery po úspěšném spuštění
                                document.removeEventListener('click', startVideoOnInteraction);
                                document.removeEventListener('touchstart', startVideoOnInteraction);
                                document.removeEventListener('scroll', startVideoOnInteraction);
                            })
                            .catch(err => {
                                console.error('Failed to start video even after interaction:', err);
                            });
                    };

                    // Naslouchat na první interakci uživatele
                    document.addEventListener('click', startVideoOnInteraction, { once: true });
                    document.addEventListener('touchstart', startVideoOnInteraction, { once: true });
                    document.addEventListener('scroll', startVideoOnInteraction, { once: true });
                });
        }

        // Zajistit, že video má správné atributy (pro jistotu)
        videoElement.setAttribute('muted', '');
        videoElement.setAttribute('playsinline', '');
        videoElement.muted = true;
        videoElement.playsInline = true;
    }
});
