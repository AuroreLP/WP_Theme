<div class="other-single">
    <h3>Autres chroniques</h3>
    <ul class="other-single-container">
        <?php 
        // Création de la requête WP_Query
        $args = array(
            'post_type'      => 'chroniques',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array(get_the_ID()), // Exclut l'article en cours
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
                
                $genres = wp_get_post_terms(get_the_ID(), 'chronique_genre');

                $genre_principal = null;

                if (!is_wp_error($genres) && !empty($genres)) {
                    $genre_principal = $genres[0];
                }
                
                get_template_part(
                    'inc/template-parts/components/cards',
                    'chronique',
                    [
                        'genre' => $genre_principal ? $genre_principal->name : ''
                    ]
                );

            endwhile; 
        endif; 
        wp_reset_postdata();
        ?>
    </ul>
</div>
