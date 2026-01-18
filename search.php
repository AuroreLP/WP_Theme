<?php
/**
 * Template pour les résultats de recherche
 */
get_header();

$term = get_queried_object();
?>

    <main class="search-archive">
        <div class="search-header">
            <h1>Résultats pour : "<?php echo esc_html(get_search_query()); ?>"</h1>
            <p><?php echo esc_html($wp_query->found_posts); ?> résultat(s) trouvé(s)</p>
            <hr>
        </div>

        <div class="search-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="search-card">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="search-thumbnail">
                                    <?php the_post_thumbnail('medium'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="search-info">
                                <h2><?php the_title(); ?></h2>
                                <span class="search-date"><?php echo esc_html(get_the_date('d.m.Y')); ?></span>
                                
                                <?php if (has_excerpt()) : ?>
                                <p class="chronique-excerpt">
                                    <?php the_excerpt(); ?>
                                </p>
                                <?php endif; ?>
                                
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
                
                <div class="pagination">
                    <?php 
                    echo paginate_links(array(
                        'prev_text' => '« Précédent',
                        'next_text' => 'Suivant »',
                    )); 
                    ?>
                </div>
                
            <?php else : ?>
            <div class="no-results">
                <p>Aucun résultat trouvé pour "<?php echo esc_html(get_search_query()); ?>".</p>
                <p>Suggestions :</p>
                <ul>
                    <li>Vérifiez l'orthographe de vos mots-clés</li>
                    <li>Essayez des mots-clés différents</li>
                    <li>Essayez des mots-clés plus généraux</li>
                </ul>
                
                <div class="search-again">
                    <form action="<?php echo esc_url(home_url('/')); ?>" method="get">
                        <input type="search" name="s" placeholder="Nouvelle recherche..." required>
                        <button type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </main>

<?php get_footer(); ?>