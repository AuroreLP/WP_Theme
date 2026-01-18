<?php get_header(); ?>

<main class="content">
    <div class="container">
        <div class="heading"><h1><?php echo wp_kses_post(get_field('home_title_section')); ?></h1></div>
        <div class="posts-grid">
            <?php
            $args = array(
                'post_type' => array('post', 'chroniques'),
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $query = new WP_Query($args);

            if ($query->have_posts()):
                while ($query->have_posts()): $query->the_post();

                    // ==================== CHRONIQUES ====================
                    if(get_post_type() === 'chroniques'):
                    $genre_info = get_chronique_genre_display();
                    $genre_name = $genre_info ? $genre_info['name'] : 'Non classé';
                    $genre_class = $genre_info ? $genre_info['slug'] : '';
            ?>
            <article class="post-box chroniques <?php echo esc_attr($genre_class); ?>" data-category="chroniques">
                <div class="article-img">
                    <?php if(has_post_thumbnail()): ?>
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>
                </div>
                <div class="article-presentation">
                    <span class="category"><?php echo esc_html($genre_name); ?></span>
                    
                    <h2 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <span class="article-date"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                    
                    <div class="article-content">
                        <p class="article-intro"><?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?></p>
                        <a class="article-btn" href="<?php the_permalink(); ?>">
                            <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                    
                    <!-- Thèmes -->
                    <?php if (get_chronique_themes()): ?>
                    <div class="article-tags article-tags--no-links">
                        <ul>
                            <?php display_chronique_themes_list(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </article>

            <?php
                    // ==================== ARTICLES ====================
                    elseif(get_post_type() === 'post'):
                        $categories = get_the_category();
                        $cat_class = $categories ? $categories[0]->slug : '';
            ?>
            <article class="post-box articles <?php echo esc_attr($cat_class); ?>" data-category="articles">
                <div class="article-img">
                    <?php if(has_post_thumbnail()): ?>
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>
                </div>
                <div class="article-presentation">
                    <span class="category"><?php echo esc_html($categories[0]->name ?? 'Non classé'); ?></span>
                    
                    <h2 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <span class="article-date"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                    
                    <div class="article-content">
                        <p class="article-intro"><?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?></p>
                        <a class="article-btn" href="<?php the_permalink(); ?>">
                            <ion-icon name="arrow-forward-outline"></ion-icon>
                        </a>
                    </div>
                    
                    <!-- Tags -->
                    <?php
                    $tags = get_the_tags();
                    if($tags && !is_wp_error($tags)):
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
                    endif;
                endwhile;
                wp_reset_postdata();
            else:
                echo '<p>' . esc_html('Aucun article ou chronique trouvé') . '</p>';
            endif;
            ?>

        </div>
        

        <nav aria-label="nav-pagination" class="nav-pagination">
            <ul class="pagination"></ul>
        </nav>
    </div>
</main>

<?php get_footer(); ?>