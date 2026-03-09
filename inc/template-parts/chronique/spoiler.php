<?php
/**
 * Template Part — Spoiler Section
 *
 * Renders the optional spoiler content behind a <details> toggle.
 * Content comes from the '_chroniques_spoiler' meta field, entered
 * via the admin meta box in post-types.php.
 *
 * Uses the native HTML <details>/<summary> elements for the toggle,
 * which work without JavaScript. The class 'wp-block-details' reuses
 * Gutenberg's built-in styling for consistency with block content.
 *
 * Basic HTML is allowed in the spoiler content via wp_kses_post().
 * Displays nothing if no spoiler content is set.
 *
 * Used in: single-chroniques.php
 *
 * @package turningpages
 */

$spoiler = get_post_meta( get_the_ID(), '_chroniques_spoiler', true );

if ( ! empty( $spoiler ) ) : ?>
    <h2>Avis avec SPOILER</h2>
    <details class="wp-block-details">
        <summary>Clique ici pour te faire spoiler</summary>
        <div class="spoiler-content">
            <?php echo wp_kses_post( $spoiler ); ?>
        </div>
    </details>
<?php endif; ?>