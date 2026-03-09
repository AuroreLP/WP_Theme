/**
 * Filter & Pagination — Articles (page-articles.php)
 *
 * Client-side filtering by category and pagination for the articles
 * listing page. All articles are already in the DOM (posts_per_page = -1).
 *
 * Filter: single <select> for category (#filter-category).
 * Each .post-box has a data-category attribute set by the PHP template.
 *
 * Pagination: 8 posts per page, generated dynamically.
 * Includes a smooth scroll-to-top on page change.
 *
 * NOTE: This script is loaded exclusively on page-articles.php
 * (via conditional enqueue in enqueue.php). The elseif chain ensures
 * it doesn't conflict with filter-chroniques.js or filter-artistes.js.
 *
 * @package turningpages
 */

document.addEventListener( 'DOMContentLoaded', function () {

    var posts               = document.querySelectorAll( '.post-box' );
    var filterSelect        = document.getElementById( 'filter-category' );
    var paginationContainer = document.querySelector( '.pagination' );

    if ( ! filterSelect || ! paginationContainer ) {
        return;
    }

    var postsPerPage  = 8;
    var currentPage   = 1;
    var allPosts      = Array.from( posts );
    var filteredPosts = allPosts.slice();

    /**
     * Show only the posts for the current page.
     * Hides all posts first, then reveals the correct slice.
     */
    function showPosts() {
        allPosts.forEach( function ( post ) {
            post.style.display = 'none';
        });

        var start     = ( currentPage - 1 ) * postsPerPage;
        var end       = start + postsPerPage;
        var pagePosts = filteredPosts.slice( start, end );

        pagePosts.forEach( function ( post ) {
            post.style.display = 'flex';
        });

        generatePagination();
    }

    /**
     * Build pagination buttons based on the filtered post count.
     */
    function generatePagination() {
        paginationContainer.innerHTML = '';
        var totalPages = Math.ceil( filteredPosts.length / postsPerPage );

        for ( var i = 1; i <= totalPages; i++ ) {
            ( function ( page ) {
                var li = document.createElement( 'li' );
                li.classList.add( 'page-item' );
                li.innerHTML = '<a href="#" class="page-link">' + page + '</a>';

                if ( page === currentPage ) {
                    li.classList.add( 'active' );
                }

                li.querySelector( 'a' ).addEventListener( 'click', function ( e ) {
                    e.preventDefault();
                    currentPage = page;
                    showPosts();
                    scrollToTop();
                });

                paginationContainer.appendChild( li );
            })( i );
        }
    }

    /**
     * Filter posts by the selected category.
     * Compares the <select> value against each post's data-category attribute.
     */
    function filterPosts() {
        var selectedCategory = filterSelect.value;

        filteredPosts = allPosts.filter( function ( post ) {
            var postCategory = post.getAttribute( 'data-category' );
            return selectedCategory === 'all' || postCategory === selectedCategory;
        });

        currentPage = 1;
        showPosts();
    }

    /**
     * Smooth scroll to the top of the listing after page change.
     * Targets the .heading element with a small offset for visual comfort.
     */
    function scrollToTop() {
        var heading   = document.querySelector( '.heading' );
        var topOffset = heading ? heading.offsetTop : 0;
        window.scrollTo( { top: topOffset - 50, behavior: 'smooth' } );
    }

    // ── Bind filter and initial render ──
    filterSelect.addEventListener( 'change', filterPosts );
    showPosts();
});
