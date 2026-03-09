<?php
/**
 * Template Part — Chronique Meta Tags
 *
 * Displays the taxonomy tags (nationality, genres, themes) below the
 * chronique header as a horizontal tag list with links to archive pages.
 *
 * Used in: single-chroniques.php (via get_template_part)
 *
 * Nationalities are rendered inline here because they need a custom
 * space before the name. Genres and themes use the factorized helpers
 * from taxonomy-helpers.php.
 *
 * @package turningpages
 */
?>

<div class="chronique-meta">
    <div class="article-tags">
        <ul>
            <?php
            /**
             * Nationalities — rendered directly (not via helper) because
             * the markup includes a leading space before the name for
             * visual spacing that the generic helper doesn't handle.
             */
            $nationalites = get_the_terms( get_the_ID(), 'nationalite' );
            if ( $nationalites && ! is_wp_error( $nationalites ) ) {
                foreach ( $nationalites as $nationalite ) {
                    echo '<li><a href="' . esc_url( get_term_link( $nationalite ) ) . '"> ' . esc_html( $nationalite->name ) . '</a></li>';
                }
            }
            ?>

            <?php /* Genres — sub-genres preferred, parent as fallback */ ?>
            <?php tp_display_chronique_genres_list(); ?>

            <?php /* Themes — all assigned themes displayed */ ?>
            <?php tp_display_chronique_themes_list(); ?>
        </ul>
    </div>
</div>