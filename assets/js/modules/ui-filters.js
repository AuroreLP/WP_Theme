/**
 * UI Filter Dropdowns — Active State Styling
 *
 * Adds/removes an 'is-active' CSS class on filter <select> elements
 * when their value changes from the default "all" option.
 *
 * This is purely visual — it allows CSS to style active filters
 * differently (e.g. highlighted border, different background) so
 * users can see at a glance which filters are applied.
 *
 * The actual filtering logic is handled by the page-specific scripts
 * (filter-chroniques.js, filter-articles.js, filter-artistes.js)
 * or by the generic pagination.js.
 *
 * Loaded globally on all pages (see enqueue.php) since filter
 * dropdowns can appear on multiple page types.
 *
 * @package turningpages
 */

document.querySelectorAll( '.filters-container select' ).forEach( function ( select ) {

    select.addEventListener( 'change', function () {
        if ( select.value !== 'all' ) {
            select.classList.add( 'is-active' );
        } else {
            select.classList.remove( 'is-active' );
        }
    });

});
