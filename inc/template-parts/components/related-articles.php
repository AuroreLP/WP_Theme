<?php
/**
 * Template Part — Related Articles
 *
 * Displays up to 4 related articles at the bottom of a single post.
 * Uses a two-step strategy to always fill the grid:
 *
 * 1. PRIMARY: Articles sharing the same category as the current post.
 *    These are the most relevant recommendations.
 *
 * 2. FALLBACK: If fewer than 4 category matches are found, fill the
 *    remaining slots with the most recent articles (regardless of
 *    category), excluding any already shown.
 *
 * This ensures the section never looks empty, even for posts in
 * categories with few entries.
 *
 * Used in: single.php (standard blog posts)
 *
 * @package turningpages
 */
?>

<div class="other-single">
    <h3>Autres articles</h3>
    <ul class="other-single-container">
        <?php
        $current_id         = get_the_ID();
        $current_categories = wp_get_post_terms( $current_id, 'category', array( 'fields' => 'ids' ) );

        /**
         * Step 1: Fetch articles in the same category.
         * Excludes the current post to avoid self-recommendation.
         */
        $related_args = array(
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array( $current_id ),
        );

        if ( ! empty( $current_categories ) ) {
            $related_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $current_categories,
                ),
            );
        }

        $related_query = new WP_Query( $related_args );
        $related_ids   = array();

        if ( $related_query->have_posts() ) :
            while ( $related_query->have_posts() ) : $related_query->the_post();
                $related_ids[] = get_the_ID();

                $categories    = get_the_category();
                $category_slug = $categories[0]->slug ?? '';
                $category_name = $categories[0]->name ?? '';

                get_template_part( 'inc/template-parts/components/cards', 'article', array(
                    'category_slug' => $category_slug,
                    'category_name' => $category_name,
                ) );

            endwhile;
        endif;
        wp_reset_postdata();

        /**
         * Step 2: Fill remaining slots with recent articles.
         * Excludes both the current post and any already displayed
         * to avoid duplicates.
         */
        if ( count( $related_ids ) < 4 ) {
            $fallback_args = array(
                'post_type'      => 'post',
                'posts_per_page' => 4 - count( $related_ids ),
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post__not_in'   => array_merge( array( $current_id ), $related_ids ),
            );

            $fallback_query = new WP_Query( $fallback_args );

            if ( $fallback_query->have_posts() ) :
                while ( $fallback_query->have_posts() ) : $fallback_query->the_post();

                    $categories    = get_the_category();
                    $category_slug = $categories[0]->slug ?? '';
                    $category_name = $categories[0]->name ?? '';

                    get_template_part( 'inc/template-parts/components/cards', 'article', array(
                        'category_slug' => $category_slug,
                        'category_name' => $category_name,
                    ) );

                endwhile;
            endif;
            wp_reset_postdata();
        }
        ?>
    </ul>
</div>