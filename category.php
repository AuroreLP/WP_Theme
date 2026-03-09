<?php
/**
 * Category Archive Template
 *
 * Displays all standard posts (articles) assigned to a specific category.
 * Uses the main WordPress query — no custom WP_Query needed since
 * category archives are handled natively.
 *
 * Pagination is handled client-side by pagination.js.
 *
 * @package turningpages
 */

get_header();

$term = get_queried_object();
?>

<main class="content">
    <div class="archive-header">
        <h1>Catégorie : <?php echo esc_html( $term->name ); ?></h1>
        <hr>
    </div>

    <div class="container">
        <div class="posts-grid">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post();

                    $categories    = get_the_category();
                    $category_slug = '';
                    $category_name = '';
                    if ( $categories && ! is_wp_error( $categories ) ) {
                        $category_slug = $categories[0]->slug;
                        $category_name = $categories[0]->name;
                    }

                    get_template_part( 'inc/template-parts/components/cards', 'article', array(
                        'category_slug' => $category_slug,
                        'category_name' => $category_name,
                    ) );

                endwhile; ?>
            <?php else : ?>
                <p>Aucun article trouvé pour cette catégorie.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php /* Pagination — populated by pagination.js */ ?>
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>
</main>

<?php get_footer(); ?>