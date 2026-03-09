<?php
/**
 * Single Template — Chronique (Review Page)
 *
 * Displays a full chronique (book/film/series/podcast review) with:
 * - Header: title + author name (via Pods relation or taxonomy fallback)
 * - Meta tags: nationality, genres, themes
 * - Excerpt (if set — used as an intro/hook)
 * - Main content (Gutenberg blocks: résumé, impressions)
 * - Spoiler section (behind a <details> toggle)
 * - Sources
 * - Cover image + media-specific sidebar (loaded by sidebar.php router)
 * - Publication date
 * - Related chroniques
 * - Comments
 *
 * The sidebar is dynamically loaded based on the 'type_media' taxonomy:
 * sidebar.php checks the media type and loads the appropriate template
 * (sidebar-livre, sidebar-film, sidebar-serie, sidebar-podcast, or
 * sidebar-default as fallback).
 *
 * @package turningpages
 */

get_header();
?>

<main class="single-chronique">

<?php if ( have_posts() ) :
    while ( have_posts() ) : the_post(); ?>

    <article>

        <?php /* Title + author (Pods relation with taxonomy fallback) */ ?>
        <?php get_template_part( 'inc/template-parts/chronique/header' ); ?>

        <hr>

        <?php /* Taxonomy tags: nationality, genres, themes */ ?>
        <?php get_template_part( 'inc/template-parts/chronique/meta' ); ?>

        <?php /* Two-column layout: text content + cover/sidebar */ ?>
        <div class="chronique-content">

            <div class="chronique-text">
                <?php if ( has_excerpt() ) : ?>
                    <div class="chronique-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>

                <?php the_content(); ?>
                <?php get_template_part( 'inc/template-parts/chronique/spoiler' ); ?>
                <?php get_template_part( 'inc/template-parts/chronique/sources' ); ?>
            </div>

            <div class="chronique-image">
                <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'medium' );
                } else {
                    echo '<p>Aucune couverture disponible</p>';
                }
                ?>

                <?php
                /**
                 * Media-specific sidebar.
                 *
                 * sidebar.php acts as a router: it reads the 'type_media'
                 * taxonomy and loads the matching sidebar template.
                 * Direct file_exists check here provides an extra safety
                 * net before delegating to the router.
                 */
                $type_media      = get_the_terms( get_the_ID(), 'type_media' );
                $type_media_slug = ( $type_media && ! is_wp_error( $type_media ) ) ? $type_media[0]->slug : '';

                if ( $type_media_slug && file_exists( get_template_directory() . "/inc/template-parts/chronique/sidebar-{$type_media_slug}.php" ) ) {
                    get_template_part( 'inc/template-parts/chronique/sidebar', $type_media_slug );
                } else {
                    get_template_part( 'inc/template-parts/chronique/sidebar', 'default' );
                }
                ?>
            </div>

        </div>

        <div class="single-date">
            Chronique rédigée le <?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
        </div>

        <?php get_template_part( 'inc/template-parts/components/related-chroniques' ); ?>

        <?php /* Comments section */ ?>
        <?php if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif; ?>

    </article>

    <?php endwhile;
endif; ?>

</main>

<?php get_footer(); ?>