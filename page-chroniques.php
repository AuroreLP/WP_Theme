<?php
/*
Template Name: Liste Chroniques
*/
get_header(); 

?>

<main class="content">
    <?php if (get_field('chroniques_title_section')) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post(get_field('chroniques_title_section')); ?></h1>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <!-- Filters -->
        <div class="filters-wrapper">
            <div class="filters-container">
                <!-- Media type -->
                <select id="filter-media">
                    <option value="all">Par type de média</option>
                    <?php
                    $type_media = get_terms(array(
                        'taxonomy' => 'type_media',
                        'parent' => 0,
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    if (!empty($type_media) && !is_wp_error($type_media)) {
                        foreach ($type_media as $media) {
                            echo '<option value="' . esc_attr($media->slug) . '">' . esc_html($media->name) . '</option>';
                        }
                    }
                    ?>
                </select>

                <!-- Genre -->
                <select id="filter-genre">
                    <option value="all">Par genre</option>
                    <?php
                    $genres_principaux = get_terms(array(
                        'taxonomy' => 'genre',
                        'parent' => 0,
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    foreach ($genres_principaux as $genre) {
                        echo '<option value="' . esc_attr($genre->slug) . '">' . esc_html($genre->name) . '</option>';
                    }
                    ?>
                </select>

                <!-- Thèmes -->
                <select id="filter-theme">
                    <option value="all">Par thème</option>
                    <?php
                    $themes = get_terms(array(
                        'taxonomy' => 'theme',
                        'hide_empty' => true,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    foreach ($themes as $theme) {
                        echo '<option value="' . esc_attr($theme->slug) . '">' . esc_html($theme->name) . '</option>';
                    }
                    ?>
                </select>

                <!-- Nationalité -->
                <select id="filter-nation">
                    <option value="all">Par pays</option>
                    <?php
                    $nations = get_terms(array(
                        'taxonomy' => 'nationalite',
                        'hide_empty' => true,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    foreach ($nations as $nation) {
                        echo '<option value="' . esc_attr($nation->slug) . '">' . esc_html($nation->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- ===== BOUCLE DES CHRONIQUES ===== -->
        <div class="posts-grid">
        

        <?php
        $args = array(
            'post_type' => 'chroniques',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        );


        $query = new WP_Query($args);

        ?>

        <?php if ($query->have_posts()) : ?>

            <?php while ($query->have_posts()) : $query->the_post();

                $genre_info = get_chronique_genre_display();
                $term = $genre_info['term'] ?? null;
                $genre_principal = ($term && $term->parent) ? get_term($term->parent, 'genre') : $term;

                $themes_slugs = get_chronique_themes() ? wp_list_pluck(get_chronique_themes(), 'slug') : array();

                $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                $nation_slug = ($nations_terms && !is_wp_error($nations_terms)) ? $nations_terms[0]->slug : '';

                $media_terms = get_the_terms(get_the_ID(), 'type_media');
                $media_slug = ($media_terms && !is_wp_error($media_terms)) ? $media_terms[0]->slug : '';

                get_template_part('inc/template-parts/components/cards', 'chronique', [
                    'genre' => $genre_principal ? $genre_principal->slug : '',
                    'themes' => join(' ', $themes_slugs),
                    'nation' => $nation_slug,
                    'media' => $media_slug
                ]);

            endwhile; ?>

        <?php else : ?>

            <p>Aucune chronique trouvée</p>

        <?php endif; ?>

        </div>

    </div>

    <!-- Pagination -->
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>