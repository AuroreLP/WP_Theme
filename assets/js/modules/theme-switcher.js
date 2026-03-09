/**
 * Theme Switcher — Color Scheme Toggle
 *
 * Manages three color schemes for the site, each named after a song:
 * - "Lilac wine"  (theme-light)  — Jeff Buckley
 * - "Purple rain" (theme-dark)   — Prince
 * - "Green day"   (theme-green)  — Green Day
 *
 * How it works:
 * 1. On page load, reads the saved theme from localStorage (or defaults to light)
 * 2. Applies the theme class to both <html> and <body>
 * 3. Swaps the logo image to match the active scheme
 * 4. Updates the toggle button label text
 *
 * The initial theme class is also applied in an inline <script> in header.php
 * (before this file loads) to prevent a flash of wrong theme on first paint.
 * This script then takes over for user interactions.
 *
 * Data sources (from enqueue.php via wp_localize_script):
 * - window.themeLogos: { 'theme-light': url, 'theme-dark': url, 'theme-green': url }
 * - window.themeData:  { themePath: '/wp-content/themes/turningpages' }
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var toggle           = document.querySelector( '.theme-toggle' );
    var menu             = document.querySelector( '.theme-menu' );
    var buttons          = document.querySelectorAll( '.theme-menu button' );
    var logo             = document.getElementById( 'site-logo' );
    var currentThemeName = document.getElementById( 'current-theme-name' );

    /**
     * Logo URLs per theme — provided by wp_localize_script in enqueue.php.
     * Falls back to hardcoded paths if the localized object isn't available
     * (e.g. during local development without WordPress).
     */
    var logos = window.themeLogos || {
        'theme-light': ( window.themeData ? window.themeData.themePath : '' ) + '/assets/images/logos/purple_logo.png',
        'theme-dark':  ( window.themeData ? window.themeData.themePath : '' ) + '/assets/images/logos/light_logo.png',
        'theme-green': ( window.themeData ? window.themeData.themePath : '' ) + '/assets/images/logos/green_logo.png',
    };

    /** Human-readable theme names for the toggle button label. */
    var themeNames = {
        'theme-light': 'Lilac wine',
        'theme-dark':  'Purple rain',
        'theme-green': 'Green day',
    };

    /** All theme classes — used to strip previous theme before applying new one. */
    var allThemes = [ 'theme-light', 'theme-dark', 'theme-green' ];

    /** Swap the header logo image to match the active theme. */
    function updateLogo( theme ) {
        if ( logo && logos[ theme ] ) {
            logo.src = logos[ theme ];
        }
    }

    /** Update the toggle button text to show the active theme name. */
    function updateThemeName( theme ) {
        if ( currentThemeName && themeNames[ theme ] ) {
            currentThemeName.textContent = themeNames[ theme ];
        }
    }

    /**
     * Apply a theme: remove all theme classes, add the new one,
     * update logo and label.
     */
    function applyTheme( theme ) {
        allThemes.forEach( function ( t ) {
            document.documentElement.classList.remove( t );
            document.body.classList.remove( t );
        });

        document.documentElement.classList.add( theme );
        document.body.classList.add( theme );

        updateLogo( theme );
        updateThemeName( theme );
    }

    // ── Toggle the dropdown menu ──
    if ( toggle && menu ) {
        toggle.addEventListener( 'click', function () {
            menu.classList.toggle( 'open' );
        });
    }

    // ── Apply saved theme on load ──
    var savedTheme  = localStorage.getItem( 'user-theme' );
    var activeTheme = savedTheme || 'theme-light';
    applyTheme( activeTheme );

    // ── Theme selection buttons ──
    buttons.forEach( function ( btn ) {
        btn.addEventListener( 'click', function () {
            var theme = btn.dataset.theme;
            if ( ! theme ) {
                return;
            }

            applyTheme( theme );
            localStorage.setItem( 'user-theme', theme );

            // Close the dropdown after selection
            if ( menu ) {
                menu.classList.remove( 'open' );
            }
        });
    });
});
