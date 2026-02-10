document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.querySelector('.theme-toggle');
    const menu = document.querySelector('.theme-menu');
    const buttons = document.querySelectorAll('.theme-menu button');
    const logo = document.getElementById('site-logo');

    const logos = {
        'theme-light': '/assets/images/logos/light_logo.png',
        'theme-dark': '/assets/images/logos/dark_logo.png',
        'theme-beige': '/assets/images/logos/brown_logo.png'
    };

    // Fonction MAJ logo
    function updateLogo(theme) {

        if (!logo) return;

        // fallback si themePath absent
        const basePath = themeData?.themePath || '';

        if (logos[theme]) {
            logo.src = basePath + logos[theme];
        }
    }

    // Toggle menu
    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('open');
        });
    }

    // Chargement thème sauvegardé
    const savedTheme = localStorage.getItem('user-theme');
    const defaultTheme = 'theme-light';
    const activeTheme = savedTheme || defaultTheme;

    document.body.classList.remove('theme-light', 'theme-dark', 'theme-beige');
    document.body.classList.add(activeTheme);

    updateLogo(activeTheme);

    // Gestion boutons
    buttons.forEach(btn => {

        btn.addEventListener('click', () => {

            const theme = btn.dataset.theme;

            if (!theme) return;

            document.body.classList.remove(
                'theme-light',
                'theme-dark',
                'theme-beige'
            );

            document.body.classList.add(theme);
            localStorage.setItem('user-theme', theme);

            updateLogo(theme);

            if (menu) {
                menu.classList.remove('open');
            }
        });

    });

});
