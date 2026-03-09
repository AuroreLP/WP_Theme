/**
 * Filter & Pagination — Artistes (page-artistes.php)
 *
 * Client-side filtering by role and nationality, with pagination,
 * for the artistes listing page.
 *
 * Filters:
 * - #filter-role:   matches data-role (uses term NAME, not slug,
 *                    to preserve point médian characters like "Auteur·ice")
 * - #filter-nation: matches data-nation (uses slug)
 *
 * Pagination: 8 posts per page, generated dynamically.
 *
 * Pattern is identical to filter-chroniques.js — both could be
 * refactored into a single configurable module in the future.
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var posts      = Array.from( document.querySelectorAll( '.post-box.artiste' ) );
    var pagination = document.querySelector( '.pagination' );
    var filters    = [ 'filter-role', 'filter-nation' ]
        .map( function ( id ) { return document.getElementById( id ); } );

    // Bail if required elements are missing
    if ( filters.indexOf( null ) !== -1 || ! pagination ) {
        return;
    }

    var postsPerPage = 8;
    var currentPage  = 1;

    /**
     * Return posts matching all active filters.
     * A post must pass every filter to be included (AND logic).
     */
    function getFiltered() {
        var role   = filters[0].value;
        var nation = filters[1].value;

        return posts.filter( function ( post ) {
            var pRole   = post.dataset.role;
            var pNation = post.dataset.nation;

            return ( role === 'all' || pRole === role )
                && ( nation === 'all' || pNation === nation );
        });
    }

    /** Hide all posts, then show only the given subset. */
    function showPosts( items ) {
        posts.forEach( function ( p ) { p.style.display = 'none'; } );
        items.forEach( function ( p ) { p.style.display = 'flex'; } );
    }

    /** Generate pagination buttons for the filtered result set. */
    function paginate( items ) {
        pagination.innerHTML = '';
        var totalPages = Math.ceil( items.length / postsPerPage );

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
                    apply();
                });
                pagination.appendChild( li );
            })( i );
        }
    }

    /** Main render cycle: filter → paginate → display. */
    function apply() {
        var filtered = getFiltered();
        var start    = ( currentPage - 1 ) * postsPerPage;
        var visible  = filtered.slice( start, start + postsPerPage );

        paginate( filtered );
        showPosts( visible );
    }

    // ── Bind filters and initial render ──
    filters.forEach( function ( f ) {
        f.addEventListener( 'change', function () {
            currentPage = 1;
            apply();
        });
    });

    apply();
});
