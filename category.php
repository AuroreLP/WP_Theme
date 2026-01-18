<?php
/**
 * Template pour afficher les articles par tag
 */
get_header(); ?>

<main class="articles-archive">
    <div class="archive-header">
        <h1>Catégorie : <?php single_cat_title(); ?></h1>
        <hr>
    </div>

    <div class="articles-grid">
        
        <?php
        if (have_posts()):
            while (have_posts()): the_post();
        ?>

        <article class="article-card">
            <a href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="article-thumbnail">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="article-info">
                    <h2><?php the_title(); ?></h2>
                    <span class="article-date"><?php echo esc_html(get_the_date('d.m.Y')); ?></span>
                    
                    <?php if (has_excerpt()) : ?>
                        <p class="article-excerpt">
                            <?php the_excerpt(); ?>
                        </p>
                    <?php else : ?>
                        <p class="article-excerpt">
                            <?php echo wp_trim_words(get_the_content(), 20, '...'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </a>
        </article>
        
        <?php
            endwhile;
        ?>
        
    </div>
    
    <?php
        else:
            echo '<p>' . esc_html('Aucun article trouvé') . '</p>';
        endif;
    ?>

    <!-- ===== PAGINATION ===== -->
        <nav class="nav-pagination">
            <ul class="pagination"></ul>
        </nav>

</main>

<?php get_footer(); ?>