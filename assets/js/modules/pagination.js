/**
 * Generic Pagination + Filtering
 *
 * Client-side pagination and optional filtering for listing pages.
 * Used on the homepage (front-page.php) and generic archives where
 * the dedicated filter scripts (filter-chroniques.js, filter-articles.js)
 * are not loaded.
 *
 * How it works:
 * 1. All posts are already in the DOM (loaded with posts_per_page = -1)
 * 2. This script shows/hides posts based on the current page number
 * 3. If filter <select> elements exist on the page, it also handles
 *    filtering by data-* attributes before paginating
 *
 * NOTE: This script overlaps in functionality with filter-chroniques.js,
 * filter-articles.js, and filter-artistes.js. Those page-specific scripts
 * are loaded via elseif in enqueue.php, ensuring only one filter/pagination
 * script runs per page.
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    // ── Config ──
    var postsGrid           = document.querySelector( '.posts-grid' );
    var paginationContainer = document.querySelector( '.pagination' );
    var postsPerPage        = 8;

    if ( ! postsGrid || ! paginationContainer ) {
        return;
    }

    var posts         = Array.from( postsGrid.children );
    var currentPage   = 1;
    var filteredPosts = posts.slice(); // Working copy for filter results

    // ── Display: show only the posts for the current page ──
    function displayPosts( postsToShow, page ) {
        page = page || 1;

        // Hide all posts first
        posts.forEach( function ( p ) {
            p.style.display = 'none';
        });

        // Show only the slice for the requested page
        var start = ( page - 1 ) * postsPerPage;
        var end   = start + postsPerPage;

        postsToShow.slice( start, end ).forEach( function ( p ) {
            p.style.display = '';
        });
    }

    // ── Pagination: render page number buttons ──
    function createPagination( postsToShow ) {
        paginationContainer.innerHTML = '';
        var totalPages = Math.ceil( postsToShow.length / postsPerPage );

        for ( var i = 1; i <= totalPages; i++ ) {
            ( function ( page ) {
                var li = document.createElement( 'li' );
                li.innerHTML = '<a href="#">' + page + '</a>';

                if ( page === currentPage ) {
                    li.classList.add( 'active' );
                }

                li.addEventListener( 'click', function ( e ) {
                    e.preventDefault();
                    currentPage = page;
                    displayPosts( postsToShow, currentPage );

                    // Update active state on all pagination items
                    Array.from( paginationContainer.children ).forEach( function ( child ) {
                        child.classList.remove( 'active' );
                    });
                    li.classList.add( 'active' );
                });

                paginationContainer.appendChild( li );
            })( i );
        }
    }

    // ── Filters: listen to any filter <select> on the page ──
    var filterElements = document.querySelectorAll(
        '#filter-media, #filter-genre, #filter-theme, #filter-nation'
    );

    filterElements.forEach( function ( select ) {
        select.addEventListener( 'change', function () {

            /**
             * Apply all active filters simultaneously.
             * Each filter checks the corresponding data-* attribute
             * on the post element (e.g. #filter-genre → data-genre).
             * Themes can have multiple slugs (space-separated).
             */
            filteredPosts = posts.filter( function ( post ) {
                var show = true;

                filterElements.forEach( function ( f ) {
                    var value = f.value;
                    if ( value === 'all' ) {
                        return;
                    }

                    var dataAttr = f.id.replace( 'filter-', '' );
                    var postData = post.dataset[ dataAttr ];

                    if ( ! postData ) {
                        return;
                    }

                    // Split handles multiple slugs (e.g. data-theme="roman essai")
                    if ( ! postData.split( ' ' ).includes( value ) ) {
                        show = false;
                    }
                });

                return show;
            });

            currentPage = 1;
            displayPosts( filteredPosts, currentPage );
            createPagination( filteredPosts );
        });
    });

    // ── Initial render ──
    displayPosts( filteredPosts, currentPage );
    createPagination( filteredPosts );
});
