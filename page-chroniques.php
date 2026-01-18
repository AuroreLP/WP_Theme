<?php
/*
Template Name: Liste Chroniques
*/
get_header(); ?>

<main class="content">
    <?php if (get_field('chroniques_title_section')) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post(get_field('chroniques_title_section')); ?></h1>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="container">
        <div class="filters-container">

            <!-- Genre -->
            <select id="filter-genre">
                <option value="all">Tous les genres</option>
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
                <option value="all">Tous les thèmes</option>
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
                <option value="all">Toutes nationalités</option>
                <?php
                $nations = get_terms(array(
                    'taxonomy' => 'nationalite',
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ));
                foreach ($nations as $nation) {
                    echo '<option value="' . esc_attr($nation->slug) . '">littérature ' . esc_html($nation->name) . '</option>';
                }
                ?>
            </select>

        </div>

        <!-- ===== BOUCLE DES CHRONIQUES ===== -->
        <?php
        $args = array(
            'post_type' => 'chroniques',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) :
            echo '<div class="posts-grid">';
            while ($query->have_posts()) : $query->the_post();

                // Genre affiché et genre principal pour filtrage
                $genre_info = get_chronique_genre_display();
                $genre_affiche_name = $genre_info ? $genre_info['name'] : 'Non classé';
                $term = $genre_info['term'];
                $genre_principal = $term->parent ? get_term($term->parent, 'genre') : $term;

                // Thèmes
                $themes_slugs = get_chronique_themes() ? wp_list_pluck(get_chronique_themes(), 'slug') : array();

                // Nationalité
                $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                $nation_slug = ($nations_terms && !is_wp_error($nations_terms)) ? $nations_terms[0]->slug : '';
        ?>

        <article class="post-box chroniques"
                 data-genre-principal="<?php echo esc_attr($genre_principal->slug); ?>"
                 data-themes="<?php echo esc_attr(join(' ', $themes_slugs)); ?>"
                 data-nation="<?php echo esc_attr($nation_slug); ?>">

            <div class="article-img">
                <?php if (has_post_thumbnail()) : ?>
                    <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                <?php endif; ?>
            </div>

            <div class="article-presentation">
                <span class="category"><?php echo esc_html($genre_affiche_name); ?></span>

                <h2 class="article-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>

                <span class="article-date"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>

                <div class="article-content">
                    <p class="article-intro">
                        <?php echo wp_trim_words(get_the_excerpt(), 150, '...'); ?>
                    </p>
                    <a class="article-btn" href="<?php the_permalink(); ?>">
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                </div>

                <?php if ($themes_slugs) : ?>
                <div class="article-tags article-tags--no-links">
                    <ul>
                        <?php display_chronique_themes_list(); ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </article>

        <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>' . esc_html('Aucune chronique trouvée') . '</p>';
        endif;
        ?>

    </div>

    <!-- Pagination -->
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>
