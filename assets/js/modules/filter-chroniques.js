/**
 * Filter & Pagination — Chroniques (page-chroniques.php)
 *
 * Client-side filtering and pagination for the chroniques listing page.
 * All chroniques are already in the DOM (posts_per_page = -1).
 *
 * Filters (AND logic — all must match):
 * - #filter-genre:  matches data-genre (parent genre slug)
 * - #filter-theme:  matches data-theme (space-separated theme slugs,
 *                    a post can have multiple themes)
 * - #filter-nation: matches data-nation (nationality slug)
 * - #filter-media:  matches data-media (media type slug)
 *
 * Pagination: 8 posts per page, generated dynamically.
 *
 * Data attributes are set in PHP by page-chroniques.php when rendering
 * each card via get_template_part('cards', 'chronique', $args).
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var posts      = Array.from( document.querySelectorAll( '.post-box' ) );
    var pagination = document.querySelector( '.pagination' );
    var filters    = [ 'filter-genre', 'filter-theme', 'filter-nation', 'filter-media' ]
        .map( function ( id ) { return document.getElementById( id ); } );

    // Bail if required elements are missing
    if ( filters.indexOf( null ) !== -1 || ! pagination ) {
        return;
    }

    var postsPerPage = 8;
    var currentPage  = 1;

    /**
     * Return posts matching all active filters.
     *
     * Theme matching uses split(' ').includes() because a post can
     * belong to multiple themes (data-theme="roman essai historique").
     * All other filters are single-value comparisons.
     */
    function getFiltered() {
        var genre  = filters[0].value;
        var theme  = filters[1].value;
        var nation = filters[2].value;
        var media  = filters[3].value;

        return posts.filter( function ( post ) {
            var pGenre  = post.dataset.genre;
            var pThemes = post.dataset.theme ? post.dataset.theme.split( ' ' ) : [];
            var pNation = post.dataset.nation;
            var pMedia  = post.dataset.media;

            return ( genre === 'all' || pGenre === genre )
                && ( theme === 'all' || pThemes.indexOf( theme ) !== -1 )
                && ( nation === 'all' || pNation === nation )
                && ( media === 'all' || pMedia === media );
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
