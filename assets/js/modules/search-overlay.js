/**
 * Search Overlay — Open / Close Behavior
 *
 * Controls the full-screen search overlay defined in header.php.
 * The overlay is toggled via CSS class 'is-active' on .search-overlay.
 *
 * Trigger points:
 * - Open:  clicking the search icon button (.search-trigger) in nav.php
 * - Close: clicking the × button (.search-close) inside the overlay
 * - Close: clicking the dark backdrop (the overlay itself, not its children)
 * - Close: pressing the Escape key
 *
 * The search input is auto-focused 300ms after opening to allow the
 * CSS transition to complete before the keyboard appears (important
 * on mobile where the virtual keyboard shifts the layout).
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var searchTrigger = document.querySelector( '.search-trigger' );
    var searchOverlay = document.querySelector( '.search-overlay' );
    var searchClose   = document.querySelector( '.search-close' );
    var searchInput   = document.querySelector( '.search-overlay input[type="search"]' );

    // Open: show overlay + auto-focus input
    if ( searchTrigger ) {
        searchTrigger.addEventListener( 'click', function () {
            searchOverlay.classList.add( 'is-active' );
            setTimeout( function () {
                if ( searchInput ) {
                    searchInput.focus();
                }
            }, 300 );
        });
    }

    // Close: × button
    if ( searchClose ) {
        searchClose.addEventListener( 'click', function () {
            searchOverlay.classList.remove( 'is-active' );
        });
    }

    // Close: clicking the dark backdrop (not the form content)
    if ( searchOverlay ) {
        searchOverlay.addEventListener( 'click', function ( e ) {
            if ( e.target === searchOverlay ) {
                searchOverlay.classList.remove( 'is-active' );
            }
        });
    }

    // Close: Escape key (works from anywhere on the page)
    document.addEventListener( 'keydown', function ( e ) {
        if ( e.key === 'Escape' && searchOverlay && searchOverlay.classList.contains( 'is-active' ) ) {
            searchOverlay.classList.remove( 'is-active' );
        }
    });
});
