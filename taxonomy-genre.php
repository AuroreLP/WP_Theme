<?php
/**
 * Taxonomy Archive — Genre
 *
 * Displays all chroniques assigned to a specific genre (or sub-genre).
 * Uses the main WordPress query for the taxonomy archive.
 * Pagination handled client-side by pagination.js.
 *
 * @package turningpages
 */

get_header();

$term = get_queried_object();
?>

<main class="content chroniques-archive">

    <div class="archive-header">
        <h1>Genre : <?php echo esc_html( $term->name ); ?></h1>
        <hr>
    </div>

    <div class="container">
        <?php if ( have_posts() ) : ?>
            <div class="posts-grid">
                <?php while ( have_posts() ) : the_post();

                    // Build card data-attributes (same pattern as page-chroniques.php)
                    $genre_info      = tp_get_chronique_genre_display();
                    $term_genre      = $genre_info['term'] ?? null;
                    $genre_principal = ( $term_genre && $term_genre->parent ) ? get_term( $term_genre->parent, 'genre' ) : $term_genre;

                    $chronique_themes = tp_get_chronique_themes();
                    $themes_slugs     = $chronique_themes ? wp_list_pluck( $chronique_themes, 'slug' ) : array();

                    $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                    $nation_slug   = ( $nations_terms && ! is_wp_error( $nations_terms ) ) ? $nations_terms[0]->slug : '';

                    $media_terms = get_the_terms( get_the_ID(), 'type_media' );
                    $media_slug  = ( $media_terms && ! is_wp_error( $media_terms ) ) ? $media_terms[0]->slug : '';

                    get_template_part( 'inc/template-parts/components/cards', 'chronique', array(
                        'genre'  => $genre_principal ? $genre_principal->slug : '',
                        'themes' => implode( ' ', $themes_slugs ),
                        'nation' => $nation_slug,
                        'media'  => $media_slug,
                    ) );

                endwhile; ?>
            </div>

            <nav class="nav-pagination">
                <ul class="pagination"></ul>
            </nav>

        <?php else : ?>
            <p>Aucune chronique trouvée pour ce genre.</p>
        <?php endif; ?>
    </div>

</main>

<?php get_footer(); ?>