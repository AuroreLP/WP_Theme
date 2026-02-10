<?php
/*
Template Name: Liste Artistes
*/
get_header(); ?>

<main class="content">
    <?php if (get_field('artistes_title_section')) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post(get_field('artistes_title_section')); ?></h1>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="container">
        <div class="filters-wrapper">
            <div class="filters-container">

                <!-- Rôle -->
                <select id="filter-role">
                    <option value="all">Par rôle</option> <!-- option par défaut -->
                    <?php
                    $roles = get_terms(array(
                        'taxonomy' => 'role',
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    if (!empty($roles) && !is_wp_error($roles)) {
                        foreach ($roles as $role_term) {
                            // Ici on met le name comme value pour gérer le point médian
                            echo '<option value="' . esc_attr($role_term->name) . '">' . esc_html($role_term->name) . '</option>';
                        }
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

        <!-- ===== BOUCLE DES ARTISTES ===== -->
         <div class="posts-grid">
            <?php
            $args_artistes = array(
                'post_type' => 'artiste',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            );
            $query_artistes = new WP_Query($args_artistes);

            if ($query_artistes->have_posts()) :
                while ($query_artistes->have_posts()) : $query_artistes->the_post();

                    // Rôle
                    $roles_terms = get_the_terms(get_the_ID(), 'role');
                    $role_name = (!is_wp_error($roles_terms) && !empty($roles_terms))
                        ? $roles_terms[0]->name
                        : '';

                    $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                    $nation_slug = (!is_wp_error($nations_terms) && !empty($nations_terms))
                        ? $nations_terms[0]->slug
                        : '';


            get_template_part('inc/template-parts/components/cards', 'artiste', [
                'role'   => $role_name,
                'nation' => $nation_slug
            ]);

            endwhile;
            wp_reset_postdata();
            else :
                echo '<p>' . esc_html('Aucun créateur trouvé') . '</p>';
            endif;
            ?>
        </div>
    </div>

    <!-- Pagination -->
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>