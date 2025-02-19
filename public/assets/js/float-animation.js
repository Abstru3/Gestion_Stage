function initFloatingAnimation() {
    const statCards = Array.from(document.querySelectorAll('.stat-card'));
    const MAX_VISIBLE_CARDS = 3;
    
    // Configuration
    const ANIMATION_DURATION = 500;
    
    // Cacher la 4ème carte initialement
    if (statCards.length > MAX_VISIBLE_CARDS) {
        statCards.slice(MAX_VISIBLE_CARDS).forEach(card => {
            card.style.display = 'none';
            card.classList.add('hidden-card');
        });
    }

    function addSwapIndicator(card) {
        card.style.cursor = 'pointer';
        card.setAttribute('title', 'Cliquez pour voir une autre statistique');
        
        // Vérifier si l'indicateur existe déjà
        if (!card.querySelector('.swap-indicator')) {
            const indicator = document.createElement('div');
            indicator.className = 'swap-indicator';
            indicator.innerHTML = '<i class="fas fa-sync-alt"></i>';
            card.appendChild(indicator);
        }
    }

    // Ajouter le style de curseur et indicateur de click
    statCards.slice(0, MAX_VISIBLE_CARDS).forEach(addSwapIndicator);

    function swapCards(visibleCard) {
        if (!visibleCard) return;
        
        // Trouver la carte cachée
        const hiddenCard = document.querySelector('.stat-card.hidden-card');
        if (!hiddenCard) return;

        // Sauvegarder les styles initiaux
        const visibleCardStyles = {
            display: window.getComputedStyle(visibleCard).display,
            transform: window.getComputedStyle(visibleCard).transform,
            opacity: window.getComputedStyle(visibleCard).opacity
        };

        // Animation de sortie pour la carte visible
        visibleCard.style.transition = `all ${ANIMATION_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1)`;
        visibleCard.style.transform = 'scale(0.8) translateY(20px)';
        visibleCard.style.opacity = '0';

        setTimeout(() => {
            // Échanger les positions dans le DOM
            const parent = visibleCard.parentNode;
            const visibleIndex = Array.from(parent.children).indexOf(visibleCard);
            const hiddenIndex = Array.from(parent.children).indexOf(hiddenCard);
            
            // Cacher la carte visible
            visibleCard.style.display = 'none';
            visibleCard.classList.add('hidden-card');
            
            // Montrer la carte cachée avec les styles originaux
            hiddenCard.style.display = visibleCardStyles.display;
            hiddenCard.classList.remove('hidden-card');
            hiddenCard.style.transform = 'scale(0.8) translateY(20px)';
            hiddenCard.style.opacity = '0';
            
            // Ajouter l'indicateur de changement à la nouvelle carte
            addSwapIndicator(hiddenCard);
            
            // Animation d'entrée pour la carte cachée
            requestAnimationFrame(() => {
                hiddenCard.style.transition = `all ${ANIMATION_DURATION}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                hiddenCard.style.transform = 'scale(1) translateY(0)';
                hiddenCard.style.opacity = '1';
            });

            // Maintenir l'ordre des cartes dans le DOM
            if (visibleIndex < hiddenIndex) {
                parent.insertBefore(hiddenCard, visibleCard);
            } else {
                parent.insertBefore(hiddenCard, visibleCard.nextSibling);
            }
        }, ANIMATION_DURATION);
    }

    // Initialisation des événements
    statCards.forEach(card => {
        let isAnimating = false;
        
        card.addEventListener('click', () => {
            if (isAnimating || card.classList.contains('hidden-card')) return;
            
            isAnimating = true;
            swapCards(card);
            
            setTimeout(() => {
                isAnimating = false;
            }, ANIMATION_DURATION * 2);
        });
    });
}

// Ajouter le CSS nécessaire
const style = document.createElement('style');
style.textContent = `
    .stat-card {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }

    .swap-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        padding: 5px;
        font-size: 0.8em;
        z-index: 1;
    }

    .stat-card:hover .swap-indicator {
        opacity: 1;
    }

    .stat-card.hidden-card {
        display: none;
    }

    .stat-card i:not(.fa-sync-alt) {
        font-size: 2.5rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .stat-card h3 {
        font-size: 2rem;
        color: var(--primary-color);
        margin: 0.5rem 0;
    }

    .stat-card p {
        color: #666;
        margin: 0;
    }
`;
document.head.appendChild(style);

// Initialiser l'animation au chargement
document.addEventListener('DOMContentLoaded', initFloatingAnimation);