document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.querySelector('.theme-toggle');
    const menu = document.querySelector('.theme-menu');
    const buttons = document.querySelectorAll('.theme-menu button');
    const logo = document.getElementById('site-logo');
    const currentThemeName = document.getElementById('current-theme-name');

    const logos = window.themeLogos || {
        'theme-light': themeData?.themePath + '/assets/images/logos/purple_logo.png',
        'theme-dark': themeData?.themePath + '/assets/images/logos/light_logo.png',
        'theme-green': themeData?.themePath + '/assets/images/logos/green_logo.png'
    };

    const themeNames = {
        'theme-light': 'Lilac wine',
        'theme-dark': 'Purple rain',
        'theme-green': 'Green day'
    };

    // Fonction MAJ logo
    function updateLogo(theme) {
        if (!logo) return;
        // fallback si themePath absent
        if (logos[theme]) {
            logo.src = logos[theme];
        }
    }

    // Fonction MAJ nom du thème
    function updateThemeName(theme) {
        if (currentThemeName && themeNames[theme]) {
            currentThemeName.textContent = themeNames[theme];
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

    document.documentElement.classList.remove('theme-light', 'theme-dark', 'theme-green');
    document.body.classList.remove('theme-light', 'theme-dark', 'theme-green');

    document.documentElement.classList.add(activeTheme);
    document.body.classList.add(activeTheme);

    updateLogo(activeTheme);
    updateThemeName(activeTheme);

    // Gestion boutons
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = btn.dataset.theme;
            if (!theme) return;

            document.documentElement.classList.remove('theme-light', 'theme-dark', 'theme-green');
            document.body.classList.remove('theme-light', 'theme-dark', 'theme-green');

            document.documentElement.classList.add(theme);
            document.body.classList.add(theme);

            localStorage.setItem('user-theme', theme);

            updateLogo(theme);
            updateThemeName(theme);

            if (menu) {
                menu.classList.remove('open');
            }
        });

    });

});
