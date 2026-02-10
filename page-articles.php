<?php
/*
Template Name: Liste Articles
*/
get_header(); ?>

<main class="content">

    <!-- ===== TITRE DE LA PAGE ===== -->
    <?php if (get_field('parentheses_title_section')) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post(get_field('parentheses_title_section')); ?></h1>
        </div>
    <?php endif; ?>

    <!-- ===== FILTRES ===== -->
    <div class="container">
        <div class="filters-wrapper">
            <div class="filters-container">
                <!-- Catégories principales -->
                <select id="filter-category">
                    <option value="all">Toutes les catégories</option>
                    <?php
                    $categories_principales = get_terms(array(
                        'taxonomy' => 'category',
                        'parent' => 0,
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    foreach ($categories_principales as $cat) {
                        echo '<option value="' . esc_attr($cat->slug) . '">' . esc_html($cat->name) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- ===== BOUCLE DES ARTICLES ===== -->
         <div class="posts-grid"> 
            <?php
            $args = array(
                'post_type'      => 'post',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) :
                    $query->the_post();

                    // Catégorie principale
                    $categories = get_the_category();
                    $category_slug = '';
                    $category_name = '';

                    if ($categories && !is_wp_error($categories)) {
                        $category_slug = $categories[0]->slug;
                        $category_name = $categories[0]->name;
                    }

            get_template_part(
                'inc/template-parts/components/cards',
                'article',
                [
                    'category_slug' => $category_slug,
                    'category_name' => $category_name
                ]
            );

        endwhile;
        
        wp_reset_postdata();
    else :
        echo '<p>' . esc_html('Aucun article trouvé') . '</p>';
    endif;
    ?>
    </div>

    <!-- ===== PAGINATION ===== -->
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>
