document.addEventListener('DOMContentLoaded', () => {
    const searchTrigger = document.querySelector('.search-trigger');
    const searchOverlay = document.querySelector('.search-overlay');
    const searchClose = document.querySelector('.search-close');
    const searchInput = document.querySelector('.search-overlay input[type="search"]');

    // Ouvrir la recherche
    if (searchTrigger) {
        searchTrigger.addEventListener('click', () => {
            searchOverlay.classList.add('is-active');
            // Focus automatique sur l'input
            setTimeout(() => searchInput?.focus(), 300);
        });
    }

    // Fermer la recherche
    if (searchClose) {
        searchClose.addEventListener('click', () => {
            searchOverlay.classList.remove('is-active');
        });
    }

    // Fermer en cliquant sur le fond noir
    if (searchOverlay) {
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) {
                searchOverlay.classList.remove('is-active');
            }
        });
    }

    // Fermer avec la touche Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && searchOverlay.classList.contains('is-active')) {
            searchOverlay.classList.remove('is-active');
        }
    });
});
