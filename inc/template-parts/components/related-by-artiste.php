<div class="other-single">
    <h3>Ses œuvres</h3>
    <ul class="other-single-container">
        <?php
        $artiste_id = get_the_ID();

        // Requête pour récupérer les posts liés à cet artiste
        $args = [
            'post_type'      => ['chroniques', 'post'],
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => 'artistes_lies',
                    'value'   => $artiste_id,
                    'compare' => 'LIKE',
                ],
            ],
        ];

        $query_artistes = new WP_Query($args);

        if ($query_artistes->have_posts()) :
            while ($query_artistes->have_posts()) : $query_artistes->the_post();

                // Récupérer le rôle
                $roles_terms = get_the_terms(get_the_ID(), 'role');
                $role_name = (!is_wp_error($roles_terms) && !empty($roles_terms))
                    ? $roles_terms[0]->name
                    : '';

                // Récupérer la nationalité
                $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                $nation_slug = (!is_wp_error($nations_terms) && !empty($nations_terms))
                    ? $nations_terms[0]->slug
                    : '';

                // Inclure la carte
                get_template_part(
                    'inc/template-parts/components/cards',
                    'artiste',
                    [
                        'role'   => $role_name,
                        'nation' => $nation_slug,
                    ]
                );

            endwhile;
            wp_reset_postdata();
        else :
        ?>
            <p class="aucune-oeuvre">Aucune œuvre chroniquée pour le moment.</p>
        <?php endif; ?>
    </ul>
</div>
