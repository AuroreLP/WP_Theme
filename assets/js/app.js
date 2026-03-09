/**
 * Main Application Script
 *
 * Handles global UI interactions that are needed on every page.
 * Currently: mobile menu toggle for the posts filter navigation.
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    /* ── Mobile menu toggle ── */
    const menuToggle = document.querySelector( '.menu-toggle' );
    const menu       = document.querySelector( '.posts-filter ul' );

    if ( menuToggle && menu ) {
        menuToggle.addEventListener( 'click', function () {
            menuToggle.classList.toggle( 'is-active' );
            menu.classList.toggle( 'is-active' );
        });
    }

});
