<?php
/**
 * Template pour afficher les chroniques par nationalité
 */
get_header();

$term = get_queried_object();
?>

<main class="content chroniques-archive">

    <div class="archive-header">
        <h1>Pays : <?php echo esc_html($term->name); ?></h1>
        <hr>
    </div>

    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="posts-grid">

                <?php while (have_posts()) : the_post();

                    // Genre affiché et genre principal pour filtrage
                    $genre_info = get_chronique_genre_display();
                    $term_genre = $genre_info['term'] ?? null;
                    $genre_principal = ($term_genre && $term_genre->parent) ? get_term($term_genre->parent, 'genre') : $term_genre;

                    // Thèmes
                    $themes_slugs = get_chronique_themes() ? wp_list_pluck(get_chronique_themes(), 'slug') : array();

                    // Nationalité (courante)
                    $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                    $nation_slug = ($nations_terms && !is_wp_error($nations_terms)) ? $nations_terms[0]->slug : '';

                    // Type de média
                    $media_terms = get_the_terms(get_the_ID(), 'type_media');
                    $media_slug = ($media_terms && !is_wp_error($media_terms)) ? $media_terms[0]->slug : '';

                    // Appel du template part
                    get_template_part(
                        'inc/template-parts/components/cards',
                        'chronique',
                        [
                            'genre'  => $genre_principal ? $genre_principal->slug : '',
                            'themes' => join(' ', $themes_slugs),
                            'nation' => $nation_slug,
                            'media'  => $media_slug
                        ]
                    );

                endwhile; ?>

            </div>

            <!-- Pagination -->
            <nav class="nav-pagination">
                <ul class="pagination"></ul>
            </nav>

        <?php else : ?>
            <p>Aucune chronique trouvée pour cette nationalité.</p>
        <?php endif; ?>
    </div>

</main>

<?php get_footer(); ?>
