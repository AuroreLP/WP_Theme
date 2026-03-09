<?php 

/*
 * Main Index Template
 *
 * Fallback template used for the blog homepage (is_home) and any archive
 * pages that don't have a more specific template file.
 *
 * WordPress template hierarchy: index.php is the last resort.
 */

get_header(); ?>

<?php if (!is_search()) : ?>

<main class="content">
    <h1><?php 
        if (is_home()) {
            echo 'Blog littéraire';
        } else {
            echo 'Archives';
        }
    ?></h1>

    <div class="container">
        <div class="heading"><h2>Mes <span>chroniques</span></h2></div>
        
        <?php
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => 8,
            'paged'          => $paged,
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post();

                // Retrieve categories once, then check before using
                $categories     = get_the_category();
                $category_class = '';
                $category_name  = '';

                if ( ! empty( $categories ) ) {
                    $category_class = $categories[0]->slug;
                    $category_name  = $categories[0]->name;
                }
        ?>
        
        <!-- Single post cards -->
        <article class="article post-box <?php echo esc_attr($category_class); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
            <div class="article-img">
                <?php
                /**
                 * Display post thumbnail with proper attributes.
                 *
                 * Using wp_get_attachment_image() instead of raw <img> tag because:
                 * - It outputs width/height attributes (reduces CLS / layout shift)
                 * - It generates srcset/sizes for responsive images automatically
                 * - It handles lazy loading natively (loading="lazy" since WP 5.5)
                 * - It escapes the alt attribute properly
                 */
                echo wp_get_attachment_image(
                    get_post_thumbnail_id(),
                    'medium_large',
                    false,
                    array( 'class' => 'article-thumbnail' )
                );
                ?>
            </div>
            <?php endif; ?>
            
            <div class="article-presentation">
                <span class="category"><?php echo esc_html($categories[0]->name); ?></span>
                <h2 class="article-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <span class="article-date">
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date( 'd.m.Y' ) ); ?>
                    </time>
                </span>
                
                <div class="article-content">
                    <p class="article-intro">
                        <?php
                        echo esc_html( wp_trim_words( get_the_excerpt(), 60, '&hellip;' ) );
                        ?>
                    </p>
                    <a class="article-btn" href="<?php the_permalink(); ?>">
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                </div>
                
                <div class="article-bottom">
                    <div class="article-tags">
                        <ul>
                            <?php
                            // Display tags if any exist
                            $tags = get_the_tags();
                            if ($tags):
                                foreach ($tags as $tag): ?>
                                    <li><a href="<?php echo get_tag_link($tag->term_id); ?>">#<?php echo $tag->name; ?></a></li>
                                <?php endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </article>
        
        <?php
            endwhile;

            the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => '&laquo; Précédent',
                'next_text' => 'Suivant &raquo;',
            ) );

            // Clean up the custom query so it doesn't interfere with
            // other template parts (sidebar, footer widgets, etc.)
            wp_reset_postdata();

        else : ?>
            <p><?php esc_html_e( 'Aucun article trouvé', 'turningpages' ); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php endif; // End !is_search() check ?>

<?php get_footer(); ?>