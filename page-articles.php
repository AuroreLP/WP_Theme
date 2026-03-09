<?php
/**
 * Template Name: Liste Articles
 *
 * Custom page template for the articles (blog posts) listing page.
 * Displays all standard WordPress posts with the ability for the visitor to filter
 * by category (client-side filtering).
 *
 * Same client-side filtering pattern as page-chroniques.php:
 * - All posts loaded at once (posts_per_page = -1)
 * - filter-articles.js handles show/hide and pagination
 *
 * @package turningpages
 */

get_header(); ?>

<main class="content">

    <?php if ( get_field( 'parentheses_title_section' ) ) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post( get_field( 'parentheses_title_section' ) ); ?></h1>
        </div>
    <?php endif; ?>

    <div class="container">

        <?php /* ── Category filter — values read by filter-articles.js ── */ ?>
        <div class="filters-wrapper">
            <div class="filters-container">
                <select id="filter-category">
                    <option value="all">Toutes les catégories</option>
                    <?php
                    // Only top-level categories (parent = 0) in the dropdown
                    $categories_principales = get_terms( array(
                        'taxonomy'   => 'category',
                        'parent'     => 0,
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                    if ( ! empty( $categories_principales ) && ! is_wp_error( $categories_principales ) ) {
                        foreach ( $categories_principales as $cat ) {
                            echo '<option value="' . esc_attr( $cat->slug ) . '">' . esc_html( $cat->name ) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <?php
        /**
         * Articles loop — all posts, newest first.
         * All loaded at once for client-side filtering.
         */
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $query = new WP_Query( $args );
        ?>

        <div class="posts-grid">
            <?php if ( $query->have_posts() ) : ?>

                <?php while ( $query->have_posts() ) : $query->the_post();

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

                <?php wp_reset_postdata(); ?>

            <?php else : ?>
                <p>Aucun article trouvé</p>
            <?php endif; ?>
        </div>

    </div>

    <?php /* Pagination container — populated by filter-articles.js */ ?>
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>