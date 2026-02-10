<?php
get_header();
?>

<main class="search-archive">

    <div class="heading search-header">
        <h1>Résultats pour : "<?php echo get_search_query(); ?>"</h1>
        <p><?php echo sprintf('%d résultat(s) trouvé(s)', $wp_query->found_posts); ?></p>
    </div>

    <div class="posts-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); 

            $excerpt = get_the_excerpt() ?: wp_trim_words(get_the_content(), 20, '...');

            // Déterminer la taxonomie selon le type de contenu
            $taxonomy = '';
            $term_name = '';

            switch (get_post_type()) {
                case 'chroniques':
                    $taxonomy = 'genre';
                    break;

                case 'artiste':
                    $taxonomy = 'role';
                    break;

                case 'post':
                    $taxonomy = 'category';
                    break;
            }

            // Récupérer le terme
            if ($taxonomy) {
                $terms = get_the_terms(get_the_ID(), $taxonomy);

                if ($terms && !is_wp_error($terms)) {
                    $term_name = $terms[0]->name;
                }
            }
        ?>

                <article class="post-box">
                    <!-- Image -->
                    <div class="article-img">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Texte -->
                    <div class="article-presentation">
                        <?php if ($term_name) : ?>
                            <span class="category">
                                <?php echo esc_html($term_name); ?>
                            </span>
                        <?php endif; ?>
                        <h2 class="article-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <span class="article-date"><?php echo get_the_date('d.m.Y'); ?></span>
                    </div>

                    <!-- Overlay -->
                    <div class="article-overlay">
                        <div class="overlay-content">
                            <p><?php echo esc_html($excerpt); ?></p>
                            <a class="article-btn" href="<?php the_permalink() ?>" aria-label="Lire l'article">
                                Lire l'article
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Aucun résultat trouvé pour votre recherche.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <nav class="nav-pagination">
        <?php
        echo paginate_links(array(
            'prev_text' => '«',
            'next_text' => '»',
        ));
        ?>
    </nav>

</main>

<?php get_footer(); ?>
