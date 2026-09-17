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
                // Chronique sidebar — sélection du gabarit selon le type de média
                // -----------------------------------------------------------------
                // Correspondance slug du terme type_media → fichier sidebar-{...}.php.
                // Plusieurs slugs peuvent pointer vers le même fichier quand les champs
                // à afficher sont identiques (ex: bd/essai/biographie/théâtre réutilisent
                // les champs d'un roman classique). Un slug absent de ce tableau tombe
                // sur 'default' plutôt que de risquer un mismatch silencieux avec le
                // système de fichiers.
                $sidebar_map = array(
                    'roman'        => 'livre',
                    'bd'           => 'livre',
                    'essai'        => 'livre',
                    'biographie'   => 'livre',
                    'theatre'      => 'livre',
                    'documentaire' => 'film',
                    'film'         => 'film',
                    'serie'        => 'serie',
                    'podcast'      => 'podcast',
                    'emission'     => 'emission',
                    'artiste'      => 'artiste',
                );

                $type_media      = get_the_terms( get_the_ID(), 'type_media' );
                $type_media_slug = ( $type_media && ! is_wp_error( $type_media ) ) ? $type_media[0]->slug : '';

                $sidebar_slug = isset( $sidebar_map[ $type_media_slug ] ) ? $sidebar_map[ $type_media_slug ] : 'default';

                get_template_part( 'inc/template-parts/chronique/sidebar', $sidebar_slug );
                ?>
                <?php get_template_part( 'inc/template-parts/components/share-buttons' ); ?>
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