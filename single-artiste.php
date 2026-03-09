<?php
/**
 * Single Template — Artiste (Portrait Page)
 *
 * Displays a full artist profile with:
 * - Title (artist name)
 * - Taxonomy tags (nationality, role, themes)
 * - Excerpt (short intro/quote)
 * - Biography (main content)
 * - Sources
 * - Portrait image + biographical sidebar (dates, nationality)
 * - Related works (chroniques/articles linked to this artist)
 * - Comments
 *
 * Reuses the 'single-chronique' CSS class for layout consistency
 * with chronique pages (same two-column text + image structure).
 *
 * @package turningpages
 */

get_header();
?>

<main class="single-chronique">

<?php while ( have_posts() ) : the_post();

    $post_id = get_the_ID();
?>

    <article id="artiste-<?php echo esc_attr( $post_id ); ?>" <?php post_class( 'artiste-fiche single-chronique' ); ?>>

        <h1 class="chronique-title">
            <?php the_title(); ?>
        </h1>

        <hr>

        <?php /* Taxonomy tags: nationality, role, themes */ ?>
        <div class="chronique-meta">
            <div class="article-tags">
                <ul>
                    <?php tp_display_chronique_nationalites_list(); ?>
                    <?php tp_display_chronique_roles_list(); ?>
                    <?php tp_display_chronique_themes_list(); ?>
                </ul>
            </div>
        </div>

        <?php /* Two-column layout: text content + portrait image */ ?>
        <div class="chronique-content">

            <div class="chronique-text">
                <?php if ( has_excerpt() ) : ?>
                    <div class="chronique-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>

                <h2>Biographie</h2>

                <?php the_content(); ?>
                <?php get_template_part( 'inc/template-parts/chronique/sources' ); ?>
            </div>

            <div class="chronique-image">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium', array(
                        'alt' => esc_attr( get_the_title() ),
                    ) ); ?>
                <?php else : ?>
                    <p>Aucune couverture disponible</p>
                <?php endif; ?>

                <?php get_template_part( 'inc/template-parts/chronique/sidebar-artiste' ); ?>
            </div>

        </div>

        <?php
        /**
         * Related works — chroniques and articles linked to this artist.
         * Uses get_template_part() instead of include() for child theme
         * compatibility and WordPress hook support.
         */
        get_template_part( 'inc/template-parts/components/related-by-artiste' );
        ?>

        <?php /* Comments section */ ?>
        <?php if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif; ?>

    </article>

<?php endwhile; ?>

</main>

<?php get_footer(); ?>