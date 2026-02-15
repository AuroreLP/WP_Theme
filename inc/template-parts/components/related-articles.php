<div class="other-single">
    <h3>Autres articles</h3>
    <ul class="other-single-container">
        <?php 
        // ID du post courant
        $current_id = get_the_ID();

        // Catégories du post courant
        $current_categories = wp_get_post_terms($current_id, 'category', ['fields' => 'ids']);

        // 1️⃣ WP_Query : articles du même thème
        $related_args = [
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => [$current_id],
        ];

        if (!empty($current_categories)) {
            $related_args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $current_categories,
                ]
            ];
        }

        $related_query = new WP_Query($related_args);

        $related_ids = [];

        if ($related_query->have_posts()) :
            while ($related_query->have_posts()) : $related_query->the_post();
                $related_ids[] = get_the_ID();
                
                // Affichage du template article
                $categories = get_the_category();
                $category_slug = $categories[0]->slug ?? '';
                $category_name = $categories[0]->name ?? '';

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

        // 2️⃣ Fallback : compléter avec articles récents si <4
        if (count($related_ids) < 4) {
            $fallback_args = [
                'post_type'      => 'post',
                'posts_per_page' => 4 - count($related_ids),
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post__not_in'   => array_merge([$current_id], $related_ids),
            ];

            $fallback_query = new WP_Query($fallback_args);

            if ($fallback_query->have_posts()) :
                while ($fallback_query->have_posts()) : $fallback_query->the_post();

                    $categories = get_the_category();
                    $category_slug = $categories[0]->slug ?? '';
                    $category_name = $categories[0]->name ?? '';

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
        }
        ?>

    </ul>
</div>