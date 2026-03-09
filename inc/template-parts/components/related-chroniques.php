<?php
/**
 * Template Part — Related Chroniques
 *
 * Displays up to 4 related chroniques at the bottom of a single chronique.
 * Uses the same two-step strategy as related-articles.php:
 *
 * 1. PRIMARY: Chroniques sharing the same genre as the current post.
 * 2. FALLBACK: Recent chroniques to fill remaining slots up to 4.
 *
 * Each card receives the full set of filter data-attributes expected
 * by cards-chronique.php (genre, themes, nation, media), even though
 * filtering isn't active on single pages — this keeps the card
 * component consistent and reusable.
 *
 * Used in: single-chroniques.php
 *
 * @package turningpages
 */
?>

<div class="other-single">
    <h3>Autres chroniques</h3>
    <ul class="other-single-container">
        <?php
        $current_id     = get_the_ID();
        $current_genres = wp_get_post_terms( $current_id, 'genre', array( 'fields' => 'ids' ) );

        /**
         * Step 1: Fetch chroniques in the same genre.
         */
        $related_args = array(
            'post_type'      => 'chroniques',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array( $current_id ),
        );

        if ( ! empty( $current_genres ) ) {
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'genre',
                    'field'    => 'term_id',
                    'terms'    => $current_genres,
                ),
            );
        }

        $related_query = new WP_Query( $related_args );
        $related_ids   = array();

        if ( $related_query->have_posts() ) :
            while ( $related_query->have_posts() ) : $related_query->the_post();
                $related_ids[] = get_the_ID();

                // Build the same data-attributes as page-chroniques.php
                $genre_info      = tp_get_chronique_genre_display();
                $term            = $genre_info['term'] ?? null;
                $genre_principal = ( $term && $term->parent ) ? get_term( $term->parent, 'genre' ) : $term;

                $chronique_themes = tp_get_chronique_themes();
                $themes_slugs     = $chronique_themes ? wp_list_pluck( $chronique_themes, 'slug' ) : array();

                $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                $nation_slug   = ( $nations_terms && ! is_wp_error( $nations_terms ) ) ? $nations_terms[0]->slug : '';

                $media_terms = get_the_terms( get_the_ID(), 'type_media' );
                $media_slug  = ( $media_terms && ! is_wp_error( $media_terms ) ) ? $media_terms[0]->slug : '';

                get_template_part( 'inc/template-parts/components/cards', 'chronique', array(
                    'genre'  => $genre_principal ? $genre_principal->slug : '',
                    'themes' => implode( ' ', $themes_slugs ),
                    'nation' => $nation_slug,
                    'media'  => $media_slug,
                ) );

            endwhile;
        endif;
        wp_reset_postdata();

        /**
         * Step 2: Fill remaining slots with recent chroniques.
         */
        if ( count( $related_ids ) < 4 ) {
            $fallback_args = array(
                'post_type'      => 'chroniques',
                'posts_per_page' => 4 - count( $related_ids ),
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post__not_in'   => array_merge( array( $current_id ), $related_ids ),
            );

            $fallback_query = new WP_Query( $fallback_args );

            if ( $fallback_query->have_posts() ) :
                while ( $fallback_query->have_posts() ) : $fallback_query->the_post();

                    $genre_info      = tp_get_chronique_genre_display();
                    $term            = $genre_info['term'] ?? null;
                    $genre_principal = ( $term && $term->parent ) ? get_term( $term->parent, 'genre' ) : $term;

                    $chronique_themes = tp_get_chronique_themes();
                    $themes_slugs     = $chronique_themes ? wp_list_pluck( $chronique_themes, 'slug' ) : array();

                    $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                    $nation_slug   = ( $nations_terms && ! is_wp_error( $nations_terms ) ) ? $nations_terms[0]->slug : '';

                    $media_terms = get_the_terms( get_the_ID(), 'type_media' );
                    $media_slug  = ( $media_terms && ! is_wp_error( $media_terms ) ) ? $media_terms[0]->slug : '';

                    get_template_part( 'inc/template-parts/components/cards', 'chronique', array(
                        'genre'  => $genre_principal ? $genre_principal->slug : '',
                        'themes' => implode( ' ', $themes_slugs ),
                        'nation' => $nation_slug,
                        'media'  => $media_slug,
                    ) );

                endwhile;
            endif;
            wp_reset_postdata();
        }
        ?>
    </ul>
</div>