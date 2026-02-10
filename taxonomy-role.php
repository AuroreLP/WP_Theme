<?php
/**
 * Template pour afficher les artistes par rôle
 */
get_header();

$term = get_queried_object();
?>

<main class="content artistes-archive">

    <div class="archive-header">
        <h1>Rôle : <?php echo esc_html($term->name); ?></h1>
        <hr>
    </div>

    <div class="posts-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); 

                // Nationalité
                $nations_terms = get_the_terms(get_the_ID(), 'nationalite');
                $nation_slug = ($nations_terms && !is_wp_error($nations_terms)) ? $nations_terms[0]->slug : '';

                // On appelle le template part pour artiste
                get_template_part(
                    'inc/template-parts/components/cards',
                    'artiste',
                    [
                        'role' => $term->slug, // rôle courant
                        'nation' => $nation_slug
                    ]
                );

            endwhile; ?>
        <?php else : ?>
            <p><?php echo esc_html('Aucun artiste trouvé pour ce rôle'); ?></p>
        <?php endif; ?>
    </div>

    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>
</main>

<?php get_footer(); ?>
