function initFloatingAnimation() {
    const statCards = Array.from(document.querySelectorAll('.stat-card'));
    
    // Configuration
    const ANIMATION_DURATION = 30;
    
    function findFarthestCard(sourceCard) {
        const sourceRect = sourceCard.getBoundingClientRect();
        let maxDistance = 0;
        let farthestCard = null;

        statCards.forEach(card => {
            if (card !== sourceCard) {
                const rect = card.getBoundingClientRect();
                const distance = Math.abs(rect.left - sourceRect.left);
                if (distance > maxDistance) {
                    maxDistance = distance;
                    farthestCard = card;
                }
            }
        });

        return farthestCard;
    }

    function animateCardSwap(card1, card2) {
        const rect1 = card1.getBoundingClientRect();
        const rect2 = card2.getBoundingClientRect();
        const translateX = rect2.left - rect1.left;

        // Animation
        requestAnimationFrame(() => {
            card1.style.transform = `translateX(${translateX}px) scale(0.95)`;
            card2.style.transform = `translateX(${-translateX}px) scale(0.95)`;

            setTimeout(() => {
                card1.style.transform = 'scale(1)';
                card2.style.transform = 'scale(1)';
                
                // Échange du contenu
                [card1.innerHTML, card2.innerHTML] = [card2.innerHTML, card1.innerHTML];
            }, ANIMATION_DURATION);
        });
    }

    // Initialisation
    statCards.forEach(card => {
        card.style.transition = `all ${ANIMATION_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1)`;
        card.style.cursor = 'pointer';
        
        let isAnimating = false;
        card.addEventListener('click', () => {
            if (isAnimating) return;
            
            const farthestCard = findFarthestCard(card);
            if (farthestCard) {
                isAnimating = true;
                animateCardSwap(card, farthestCard);
                
                setTimeout(() => {
                    isAnimating = false;
                }, ANIMATION_DURATION);
            }
        });
    });
}