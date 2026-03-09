<?php
/**
 * Taxonomy Archive — Rôle (Artiste Role)
 *
 * Displays all artistes assigned to a specific role
 * (e.g. Auteur·ice, Réalisateur·ice).
 * Pagination handled client-side by pagination.js.
 *
 * @package turningpages
 */

get_header();

$term = get_queried_object();
?>

<main class="content artistes-archive">

    <div class="archive-header">
        <h1>Rôle : <?php echo esc_html( $term->name ); ?></h1>
        <hr>
    </div>

    <div class="posts-grid">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post();

                $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                $nation_slug   = ( $nations_terms && ! is_wp_error( $nations_terms ) ) ? $nations_terms[0]->slug : '';

                /**
                 * Role uses term NAME (not slug) to preserve the point médian
                 * character (e.g. "Auteur·ice"). This matches the convention
                 * used in page-artistes.php and filter-artistes.js.
                 */
                get_template_part( 'inc/template-parts/components/cards', 'artiste', array(
                    'role'   => $term->name,
                    'nation' => $nation_slug,
                ) );

            endwhile; ?>
        <?php else : ?>
            <p>Aucun artiste trouvé pour ce rôle.</p>
        <?php endif; ?>
    </div>

    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>