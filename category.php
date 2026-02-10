<?php
/**
 * Template pour afficher les articles par catégorie
 */
get_header();

$term = get_queried_object();
?>

<main class="content">
    <div class="archive-header">
        <h1>Catégorie : <?php echo esc_html($term->name); ?></h1>
        <hr>
    </div>

    <div class="container">
        <div class="posts-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); 

                    // Catégorie principale
                    $categories = get_the_category();
                    $category_slug = '';
                    $category_name = '';
                    if ($categories && !is_wp_error($categories)) {
                        $category_slug = $categories[0]->slug;
                        $category_name = $categories[0]->name;
                    }

                    // Appel du template part
                    get_template_part('inc/template-parts/components/cards', 'article', [
                        'category_slug' => $category_slug,
                        'category_name' => $category_name,
                    ]);

                endwhile; ?>
            <?php else : ?>
                <p><?php echo esc_html('Aucun article trouvé pour cette catégorie.'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== PAGINATION ===== -->
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>
</main>

<?php get_footer(); ?>
