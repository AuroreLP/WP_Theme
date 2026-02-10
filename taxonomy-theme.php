<?php
/**
 * Template pour afficher les chroniques par thème
 */
get_header();

$term = get_queried_object();
?>

<main class="content chroniques-archive">

    <div class="archive-header">
        <h1>Thème : <?php echo esc_html($term->name); ?></h1>
        <hr>
    </div>

    <div class="posts-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post();

                $genre_info = get_chronique_genre_display();
                $term_genre = $genre_info['term'] ?? null;
                $genre_slug = $term_genre ? $term_genre->slug : '';

                $themes_slugs = get_chronique_themes() ? wp_list_pluck(get_chronique_themes(), 'slug') : [];
                $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                $nation_slug = ($nations_terms && !is_wp_error($nations_terms)) ? $nations_terms[0]->slug : '';

                $media_terms = get_the_terms(get_the_ID(), 'type_media');
                $media_slug = ($media_terms && !is_wp_error($media_terms)) ? $media_terms[0]->slug : '';

                // Appel template part cards-chronique
                get_template_part(
                    'inc/template-parts/components/cards',
                    'chronique',
                    [
                        'genre' => $genre_slug,
                        'themes' => join(' ', $themes_slugs),
                        'nation' => $nation_slug,
                        'media' => $media_slug
                    ]
                );

            endwhile; ?>
        <?php else : ?>
            <p><?php echo esc_html('Aucune chronique trouvée pour ce thème'); ?></p>
        <?php endif; ?>
    </div>

    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>
</main>

<?php get_footer(); ?>
