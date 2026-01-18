<?php
/**
 * Template pour afficher les chroniques par auteur
 */
get_header();

$term = get_queried_object();
?>

    <main class="chroniques-archive">
        <div class="archive-header">
            <h1>Auteur : <?php echo esc_html($term->name); ?></h1>
            <hr>
        </div>

        <div class="chroniques-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="chronique-card">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="chronique-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="chronique-info">
                                <h2><?php the_title(); ?></h2>
                                <span class="article-date"><?php echo esc_html(get_the_date('d.m.Y')); ?></span>
                                
                                <?php if (has_excerpt()) : ?>
                                <p class="chronique-excerpt">
                                    <?php the_excerpt(); ?>
                                </p>
                                <?php endif; ?>
                                
                                <?php 
                                $note = get_post_meta(get_the_ID(), 'note_etoiles', true);
                                if ($note) : ?>
                                    <div class="chronique-rating-mini">
                                        <span class="rating-value"><?php echo esc_html($note); ?>/5 ⭐</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
                
            <?php else : ?>
                <p><?php echo esc_html('Aucune chronique trouvée pour cet auteur'); ?></p>
            <?php endif; ?>
        </div>
        <!-- ===== PAGINATION ===== -->
        <nav class="nav-pagination">
            <ul class="pagination"></ul>
        </nav>
    </main>

<?php get_footer(); ?>