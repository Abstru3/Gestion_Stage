function initFloatingAnimation() {
    const statCards = Array.from(document.querySelectorAll('.stat-card'));
    
    // Configuration
    const ANIMATION_DURATION = 500; // Augmenté pour une animation plus fluide
    
    // Ajouter le style de curseur à toutes les cartes
    statCards.forEach(card => {
        card.style.cursor = 'pointer';
    });

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

        // Créer des clones pour l'animation
        const clone1 = card1.cloneNode(true);
        const clone2 = card2.cloneNode(true);

        // Positionner les clones
        Object.assign(clone1.style, {
            position: 'fixed',
            top: `${rect1.top}px`,
            left: `${rect1.left}px`,
            width: `${rect1.width}px`,
            height: `${rect1.height}px`,
            transition: `all ${ANIMATION_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1)`,
            zIndex: 1000
        });

        Object.assign(clone2.style, {
            position: 'fixed',
            top: `${rect2.top}px`,
            left: `${rect2.left}px`,
            width: `${rect2.width}px`,
            height: `${rect2.height}px`,
            transition: `all ${ANIMATION_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1)`,
            zIndex: 1000
        });

        // Ajouter les clones au DOM
        document.body.appendChild(clone1);
        document.body.appendChild(clone2);

        // Cacher les cartes originales
        card1.style.opacity = '0';
        card2.style.opacity = '0';

        // Animer les clones
        requestAnimationFrame(() => {
            clone1.style.transform = `translateX(${translateX}px)`;
            clone2.style.transform = `translateX(${-translateX}px)`;

            setTimeout(() => {
                // Échanger le contenu des cartes originales
                const temp = card1.innerHTML;
                card1.innerHTML = card2.innerHTML;
                card2.innerHTML = temp;

                // Restaurer l'opacité des originaux
                card1.style.opacity = '1';
                card2.style.opacity = '1';

                // Supprimer les clones
                clone1.remove();
                clone2.remove();
            }, ANIMATION_DURATION);
        });
    }

    // Initialisation
    statCards.forEach(card => {
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