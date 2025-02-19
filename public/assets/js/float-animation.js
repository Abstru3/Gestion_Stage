function initFloatingAnimation() {
    const statCards = Array.from(document.querySelectorAll('.stat-card'));
    const MAX_VISIBLE_CARDS = 3;
    const ANIMATION_DURATION = 100; // Augmenté pour plus de stabilité
    
    let isAnimating = false;
    let currentAnimation = null;

    // Configuration des animations
    const animationConfig = {
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        scaleOut: 'scale(0.8) translateY(20px)',
        scaleNormal: 'scale(1) translateY(0)'
    };

    function initializeCards() {
        if (statCards.length > MAX_VISIBLE_CARDS) {
            statCards.slice(MAX_VISIBLE_CARDS).forEach(card => {
                card.style.display = 'none';
                card.classList.add('hidden-card');
            });
        }
        statCards.slice(0, MAX_VISIBLE_CARDS).forEach(addSwapIndicator);
    }

    function addSwapIndicator(card) {
        if (card.querySelector('.swap-indicator')) return;
        
        const indicator = document.createElement('div');
        indicator.className = 'swap-indicator';
        indicator.innerHTML = `
            <div class="swap-icon">
                <i class="fas fa-sync-alt"></i>
            </div>
        `;
        card.appendChild(indicator);
        card.style.cursor = 'pointer';
    }

    function swapCards(visibleCard) {
        if (!visibleCard || isAnimating) return;
        
        const hiddenCard = document.querySelector('.stat-card.hidden-card');
        if (!hiddenCard) return;

        isAnimating = true;

        // Annuler l'animation précédente si elle existe
        if (currentAnimation) {
            currentAnimation.abort();
        }

        // Animation de sortie
        visibleCard.style.transition = `all ${ANIMATION_DURATION}ms ${animationConfig.easing}`;
        visibleCard.style.transform = animationConfig.scaleOut;
        visibleCard.style.opacity = '0';

        // Utiliser setTimeout au lieu de transitionend pour plus de fiabilité
        setTimeout(() => {
            // Échanger les cartes
            visibleCard.style.display = 'none';
            visibleCard.classList.add('hidden-card');
            visibleCard.style.transform = '';
            visibleCard.style.opacity = '';
            
            // Préparer la nouvelle carte
            hiddenCard.style.display = 'flex';
            hiddenCard.classList.remove('hidden-card');
            hiddenCard.style.transform = animationConfig.scaleOut;
            hiddenCard.style.opacity = '0';
            
            // Forcer un reflow
            hiddenCard.offsetHeight;

            // Animation d'entrée
            hiddenCard.style.transition = `all ${ANIMATION_DURATION}ms ${animationConfig.easing}`;
            hiddenCard.style.transform = animationConfig.scaleNormal;
            hiddenCard.style.opacity = '1';

            // Ajouter l'indicateur à la nouvelle carte
            addSwapIndicator(hiddenCard);

            // Réinitialiser l'état après l'animation
            setTimeout(() => {
                isAnimating = false;
                currentAnimation = null;
            }, ANIMATION_DURATION);
        }, ANIMATION_DURATION);
    }

    // Gestionnaire d'événements simple sans file d'attente
    statCards.forEach(card => {
        let lastClickTime = 0;
        const CLICK_DELAY = ANIMATION_DURATION * 1.2; // Délai minimum entre les clics

        card.addEventListener('click', (e) => {
            if (card.classList.contains('hidden-card')) return;
            
            const now = Date.now();
            if (now - lastClickTime < CLICK_DELAY) return; // Ignorer les clics trop rapides
            
            lastClickTime = now;
            swapCards(card);
        });
    });

    // Initialisation
    initializeCards();
}

// Styles améliorés
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