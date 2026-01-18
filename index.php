<?php get_header(); ?>

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
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1
        );
        $query = new WP_Query($args);
        
        if ($query->have_posts()):
            while ($query->have_posts()): $query->the_post();
                
                $categories = get_the_category();
                $category_class = '';
                if ($categories) {
                    $category_class = $categories[0]->slug;
                }
        ?>
        
        <!-- Article -->
        <article class="article post-box <?php echo esc_attr($category_class); ?>">
            <div class="article-img">
                <?php if (has_post_thumbnail()): ?>
                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                <?php endif; ?>
            </div>
            
            <div class="article-presentation">
                <span class="category"><?php echo esc_html($categories[0]->name); ?></span>
                <h2 class="article-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <span class="article-date"><?php echo get_the_date('d.m.Y'); ?></span>
                
                <div class="article-content">
                    <p class="article-intro">
                        <?php echo wp_trim_words(get_the_excerpt(), 150, '...'); ?>
                    </p>
                    <a class="article-btn" href="<?php the_permalink(); ?>">
                        <ion-icon name="arrow-forward-outline"></ion-icon>
                    </a>
                </div>
                
                <div class="article-bottom">
                    <div class="article-tags">
                        <ul>
                            <?php
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
            wp_reset_postdata();
        else:
            echo '<p>Aucun article trouvé</p>';
        endif;
        ?>
    </div>
</main>

<?php endif; // Fin de !is_search() ?>

</section> 


<?php get_footer(); ?>