<?php
/**
 * Template Part — Sources List
 *
 * Displays the reference sources listed in the '_post_sources' meta field.
 * Sources are entered one per line in the admin meta box (see post-types.php)
 * and rendered as a <ul> list.
 *
 * Supports basic HTML in each line (links, emphasis) via wp_kses_post(),
 * so entries like:
 *   <a href="https://example.com">Interview Cineuropa</a> (mai 2024)
 * will render as clickable links.
 *
 * Displays nothing if no sources are set.
 *
 * Used in: single-chroniques.php, single.php, single-artiste.php
 *
 * @package turningpages
 */

$sources = get_post_meta( get_the_ID(), '_post_sources', true );

if ( ! empty( $sources ) ) :
    // Split by newline, trim whitespace, remove empty lines
    $lines = array_filter( array_map( 'trim', explode( "\n", $sources ) ) );
?>
    <div class="chronique-sources">
        <h4>Sources</h4>
        <ul class="sources-list">
            <?php foreach ( $lines as $line ) : ?>
                <li><?php echo wp_kses_post( $line ); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>