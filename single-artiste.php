<?php
/**
 * Template pour afficher une fiche Artiste
 */
get_header(); 
?>

<main class="single-chronique">

<?php while (have_posts()) : the_post(); 

    $post_id = get_the_ID();

    // Taxonomie
    $roles = get_the_terms($post_id, 'role');

    // Metas
    $date_naissance_raw = get_post_meta($post_id, 'date_naissance', true);
    $date_deces_raw     = get_post_meta($post_id, 'date_deces', true);
    $nationalite        = get_post_meta($post_id, 'nationalite', true);
?>
        
    <article id="artiste-<?php echo esc_attr($post_id); ?>" <?php post_class('artiste-fiche single-chronique'); ?>>

        <h1 class="chronique-title">
            <?php the_title(); ?>
        </h1>

        <hr>

        <div class="chronique-meta">
            <div class="article-tags">
                <ul>
                    <?php display_chronique_nationalites_list(); ?>
                    <?php display_chronique_roles_list(); ?>
                    <?php display_chronique_themes_list(); ?>
                </ul>
            </div>
        </div>


        <div class="chronique-content">

            <div class="chronique-text">

                <?php if (has_excerpt()) : ?>
                    <div class="chronique-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>

                <h2>Biographie</h2>

                <?php the_content(); ?>

            </div>


            <div class="chronique-image">

                <?php if (has_post_thumbnail()) : ?>

                    <?php the_post_thumbnail('medium', [
                        'alt' => esc_attr(get_the_title())
                    ]); ?>

                <?php else : ?>

                    <p>Aucune couverture disponible</p>

                <?php endif; ?>
                <?php get_template_part('inc/template-parts/chronique/sidebar-artiste'); ?>

            </div>

        </div>
        <?php include('inc/template-parts/components/related-by-artiste.php'); ?>
        <!-- ########################## -->
        <!-- Comments section -->
        <!-- ########################## -->
        <?php
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    </article>

    <?php 
        endwhile;
    ?>

</main>

<?php get_footer(); ?>
