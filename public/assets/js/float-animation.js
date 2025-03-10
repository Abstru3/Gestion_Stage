function initFloatingAnimation() {
    const statCards = new Set(document.querySelectorAll('.stat-card'));
    const MAX_VISIBLE_CARDS = 3;
    const ANIMATION_DURATION = 300;
    
    let animationFrameId = null;
    let isAnimating = false;

    const animationConfig = {
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        transform: {
            out: 'translate3d(0, 20px, 0) scale(0.8)',
            normal: 'translate3d(0, 0, 0) scale(1)'
        }
    };

    const indicatorTemplate = document.createElement('template');
    indicatorTemplate.innerHTML = `
        <div class="swap-indicator">
            <div class="swap-icon">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    `;

    function initializeCards() {
        if (statCards.size > MAX_VISIBLE_CARDS) {
            Array.from(statCards).slice(MAX_VISIBLE_CARDS).forEach(card => {
                card.style.display = 'none';
                card.classList.add('hidden-card');
            });
        }
        Array.from(statCards).slice(0, MAX_VISIBLE_CARDS).forEach(addSwapIndicator);
    }

    function addSwapIndicator(card) {
        if (card.querySelector('.swap-indicator')) return;
        card.appendChild(indicatorTemplate.content.cloneNode(true));
        card.style.cursor = 'pointer';
    }

    function animateCard(card, properties) {
        return new Promise(resolve => {
            const onTransitionEnd = () => {
                card.removeEventListener('transitionend', onTransitionEnd);
                resolve();
            };
            
            card.addEventListener('transitionend', onTransitionEnd);
            
            requestAnimationFrame(() => {
                Object.assign(card.style, properties);
            });
        });
    }

    async function swapCards(visibleCard) {
        if (!visibleCard || isAnimating) return;
        
        const hiddenCard = document.querySelector('.stat-card.hidden-card');
        if (!hiddenCard) return;

        isAnimating = true;

        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        try {
            await animateCard(visibleCard, {
                transition: `transform ${ANIMATION_DURATION}ms ${animationConfig.easing}`,
                transform: animationConfig.transform.out,
                opacity: '0'
            });

            requestAnimationFrame(() => {
                visibleCard.style.display = 'none';
                visibleCard.classList.add('hidden-card');
                
                hiddenCard.style.display = 'flex';
                hiddenCard.classList.remove('hidden-card');
                hiddenCard.style.transform = animationConfig.transform.out;
                hiddenCard.style.opacity = '0';
                
                hiddenCard.offsetHeight;
                
                animateCard(hiddenCard, {
                    transition: `all ${ANIMATION_DURATION}ms ${animationConfig.easing}`,
                    transform: animationConfig.transform.normal,
                    opacity: '1'
                });
            });

            addSwapIndicator(hiddenCard);

        } finally {
            setTimeout(() => {
                isAnimating = false;
            }, ANIMATION_DURATION);
        }
    }

    document.addEventListener('click', (e) => {
        const card = e.target.closest('.stat-card:not(.hidden-card)');
        if (card && !isAnimating) {
            swapCards(card);
        }
    }, { passive: true });

    requestAnimationFrame(() => {
        initializeCards();
    });
}

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
        will-change: transform, opacity;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    .stat-card * {
        will-change: transform;
        transform: translateZ(0);
    }

    @media (prefers-reduced-motion: reduce) {
        .stat-card {
            transition: none !important;
            transform: none !important;
        }
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