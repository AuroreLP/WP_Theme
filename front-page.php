<?php get_header(); ?>

<main class="content">
    <div class="container">
        <div class="heading"><h1><?php echo wp_kses_post(get_field('home_title_section')); ?></h1></div>
        <div class="posts-grid">
            <?php
            $args = array(
                'post_type' => array('post', 'chroniques', 'artiste'),
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
    
                <!-- Image -->
                <div class="article-img">
                    <?php if(has_post_thumbnail()): ?>
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>
                </div>

                <!-- Texte normal -->
                <div class="article-presentation">
                    <span class="category"><?php echo esc_html($genre_name); ?></span>
                    <h2 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <span class="article-date"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                </div>

                <!-- Overlay résumé + bouton -->
                <div class="article-overlay">
                    <div class="overlay-content">
                        <p><?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?></p>
                        <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="Lire la chronique">
                            Lire l'article<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <path class="arrow-path"
                                    fill="none"
                                    stroke-width="48"
                                    stroke-linecap="square"
                                    stroke-miterlimit="10"
                                    d="M268 112l144 144-144 144M392 256H100"/>
                            </svg>
                        </a>
                    </div>
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
                </div>
                <!-- Overlay résumé + bouton -->
                <div class="article-overlay">
                    <div class="overlay-content">
                        <p><?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?></p>
                        <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="Lire l'article'">
                            Lire l'article<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <path class="arrow-path"
                                    fill="none"
                                    stroke-width="48"
                                    stroke-linecap="square"
                                    stroke-miterlimit="10"
                                    d="M268 112l144 144-144 144M392 256H100"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>

            <?php
                    // ==================== ARTISTES ====================
                    elseif(get_post_type() === 'artiste'):
                        $roles_terms = get_the_terms(get_the_ID(), 'role');
                        $role_name = ($roles_terms && !is_wp_error($roles_terms)) ? $roles_terms[0]->name : 'Créateur';
                        $role_class = ($roles_terms && !is_wp_error($roles_terms)) ? $roles_terms[0]->slug : '';
            ?>
            <article class="post-box artiste <?php echo esc_attr($role_class); ?>" data-category="artiste">
                <div class="article-img">
                    <?php if(has_post_thumbnail()): ?>
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>
                </div>
                <div class="article-presentation">
                    <span class="category"><?php echo esc_html($role_name); ?></span>
                    <h2 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <span class="article-date"><?php echo esc_html(get_the_date('d/m/Y')); ?></span>
                </div>
                <!-- Overlay résumé + bouton -->
                <div class="article-overlay">
                    <div class="overlay-content">
                        <p><?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?></p>
                        <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="Lire le portrait">
                            Lire l'article<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                                <path class="arrow-path"
                                    fill="none"
                                    stroke-width="48"
                                    stroke-linecap="square"
                                    stroke-miterlimit="10"
                                    d="M268 112l144 144-144 144M392 256H100"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>

            <?php
                    endif;
                endwhile;
                wp_reset_postdata();
            else:
                echo '<p>' . esc_html('Aucun contenu trouvé') . '</p>';
            endif;
            ?>
        </div>
        <nav aria-label="nav-pagination" class="nav-pagination">
            <ul class="pagination"></ul>
        </nav>
    </div>
</main>

<?php get_footer(); ?>