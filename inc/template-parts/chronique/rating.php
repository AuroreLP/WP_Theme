<?php
/**
 * Template Part — Star Rating Display
 *
 * Renders a star rating (0.5 to 5) using Ionicons star icons.
 * Extracted from the sidebar files where this exact block was
 * duplicated in sidebar-livre, sidebar-film, sidebar-serie, and
 * sidebar-podcast.
 *
 * Usage:
 *   get_template_part( 'inc/template-parts/chronique/rating' );
 *
 * Reads the 'note_etoiles' post meta from the current post.
 * Displays nothing if no rating is set (value is empty or 0).
 *
 * Icon breakdown:
 * - Full stars:  star         (e.g. 3 for a 3.5 rating)
 * - Half star:   star-half    (1 if the decimal part >= 0.5)
 * - Empty stars: star-outline (fills the remaining slots up to 5)
 *
 * @package turningpages
 */

$note = get_post_meta( get_the_ID(), 'note_etoiles', true );
$note = $note !== '' ? floatval( $note ) : 0;
$note = max( 0, min( 5, $note ) );

if ( $note > 0 ) :
    $note_full  = floor( $note );
    $note_half  = ( $note - $note_full ) >= 0.5 ? 1 : 0;
    $note_empty = 5 - $note_full - $note_half;
?>
<div class="chronique-rating">
    <?php
    for ( $i = 0; $i < $note_full; $i++ ) {
        echo '<ion-icon name="star" aria-hidden="true"></ion-icon>';
    }
    if ( $note_half ) {
        echo '<ion-icon name="star-half" aria-hidden="true"></ion-icon>';
    }
    for ( $i = 0; $i < $note_empty; $i++ ) {
        echo '<ion-icon name="star-outline" aria-hidden="true"></ion-icon>';
    }
    ?>
    <span class="rating-value"><?php echo esc_html( $note ); ?>/5</span>
</div>
<?php endif; ?>