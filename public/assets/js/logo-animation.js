function initLogoAnimation() {
    const logoImage = document.querySelector('.logo img');
    let opacity = 1;
    let increasing = false;
    const ANIMATION_SPEED = 0.003;
    
    function animate() {
        if (increasing) {
            opacity += ANIMATION_SPEED;
            if (opacity >= 1) {
                opacity = 1;
                increasing = false;
            }
        } else {
            opacity -= ANIMATION_SPEED;
            if (opacity <= 0.1) {
                opacity = 0.1;
                increasing = true;
            }
        }
        
        // Gradient qui fait varier l'opacité de gauche à droite
        logoImage.style.mask = `linear-gradient(
            to right,
            rgba(0, 0, 0, 1) 10%,
            rgba(0, 0, 0, ${opacity})
        )`;
        
        // Support pour tous les navigateurs
        logoImage.style.webkitMask = logoImage.style.mask;
        
        // Effet de lueur subtil
        logoImage.style.filter = `brightness(1.1)`;
        
        requestAnimationFrame(animate);
    }

    // Optimisation des performances
    logoImage.style.willChange = 'mask';
    
    // Démarrage de l'animation
    animate();
}

document.addEventListener('DOMContentLoaded', initLogoAnimation);