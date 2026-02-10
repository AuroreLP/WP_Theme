<div class="other-single">
    <h3>Autres articles</h3>
    <ul class="other-single-container">
        <?php 
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array(get_the_ID()),
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
        endif; 
        wp_reset_postdata();
        ?>
    </ul>
</div>