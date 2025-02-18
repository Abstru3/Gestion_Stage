function initLogoAnimation() {
    const logoImage = document.querySelector('.logo img');
    let position = -20;
    const ANIMATION_SPEED = 0.4;
    const LIGHT_WIDTH = 25;
    
    function animate() {
        position += ANIMATION_SPEED;
        
        if (position > 120) {
            position = -20;
        }
        
        logoImage.style.mask = `linear-gradient(
            to right,
            rgba(0, 0, 0, 0.7) ${position - LIGHT_WIDTH}%,
            rgba(0, 0, 0, 1) ${position}%,
            rgba(0, 0, 0, 0.7) ${position + LIGHT_WIDTH}%
        )`;
        
        logoImage.style.webkitMask = logoImage.style.mask;
        
        logoImage.style.filter = `brightness(1.1) contrast(1.2) saturate(1.2)`;
        
        requestAnimationFrame(animate);
    }

    logoImage.style.willChange = 'mask, filter';
    
    animate();
}

document.addEventListener('DOMContentLoaded', initLogoAnimation);