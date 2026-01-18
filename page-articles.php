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

        <!-- ===== BOUCLE DES ARTICLES ===== -->
        <?php
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) :
            echo '<div class="posts-grid">'; 
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
        ?>

        <article class="post-box articles" data-category="<?php echo esc_attr($category_slug); ?>">

            <!-- Image -->
            <div class="article-img">
                <?php if (has_post_thumbnail()) : ?>
                    <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                <?php endif; ?>
            </div>

            <!-- Contenu -->
            <div class="article-presentation">
                <?php if ($category_name) : ?>
                    <span class="category"><?php echo esc_html($category_name); ?></span>
                <?php endif; ?>

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

                <!-- Tags -->
                <?php
                $tags = get_the_tags();
                if ($tags) :
                ?>
                    <div class="article-tags article-tags--no-links">
                        <ul>
                            <?php foreach($tags as $tag): ?>
                                <li><a href="#"><?php echo esc_html($tag->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>
        </article>

        <?php
            endwhile;
            echo '</div>'; 
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
