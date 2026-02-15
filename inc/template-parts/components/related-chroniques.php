<div class="other-single">
    <h3>Autres chroniques</h3>
    <ul class="other-single-container">
        <?php 
        $current_id = get_the_ID();

        // Récupère les genres du post courant
        $current_genres = wp_get_post_terms($current_id, 'chronique_genre', ['fields' => 'ids']);

        // 1️⃣ Query : chroniques du même genre
        $related_args = [
            'post_type'      => 'chroniques',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => [$current_id],
        ];

        if (!empty($current_genres)) {
            $related_args['tax_query'] = [
                [
                    'taxonomy' => 'chronique_genre',
                    'field'    => 'term_id',
                    'terms'    => $current_genres,
                ]
            ];
        }

        $related_query = new WP_Query($related_args);
        $related_ids = [];

        if ($related_query->have_posts()) :
            while ($related_query->have_posts()) : $related_query->the_post();
                $related_ids[] = get_the_ID();

                $genres = wp_get_post_terms(get_the_ID(), 'chronique_genre');
                $genre_principal = !empty($genres) && !is_wp_error($genres) ? $genres[0]->name : '';

                // Passe aussi une classe vide par défaut pour la grille
                get_template_part(
                    'inc/template-parts/components/cards',
                    'chronique',
                    [
                        'genre' => $genre_principal,
                        'class' => ''
                    ]
                );

            endwhile;
        endif;
        wp_reset_postdata();

        // 2️⃣ Fallback : compléter avec chroniques récentes si moins de 4
        if (count($related_ids) < 4) {
            $fallback_args = [
                'post_type'      => 'chroniques',
                'posts_per_page' => 4 - count($related_ids),
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post__not_in'   => array_merge([$current_id], $related_ids),
            ];

            $fallback_query = new WP_Query($fallback_args);

            if ($fallback_query->have_posts()) :
                while ($fallback_query->have_posts()) : $fallback_query->the_post();

                    $genres = wp_get_post_terms(get_the_ID(), 'chronique_genre');
                    $genre_principal = !empty($genres) && !is_wp_error($genres) ? $genres[0]->name : '';

                    // On ajoute une classe "fallback" pour éventuellement styler différemment
                    get_template_part(
                        'inc/template-parts/components/cards',
                        'chronique',
                        [
                            'genre' => $genre_principal,
                            'class' => ' fallback'
                        ]
                    );

                endwhile;
            endif;
            wp_reset_postdata();
        }
        ?>
    </ul>
</div>
