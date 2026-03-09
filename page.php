<?php get_header(); ?>

<?php
/**
 * Default Page Template
 *
 * Fallback template for all WordPress pages that don't have a more
 * specific template assigned (e.g. pages without a "Template Name").
 *
 * Currently used for:
 * - Mentions Légales
 * - Politique de confidentialité
 * - Any other static pages without a custom template
 *
 * Uses 'container-legal' class for a narrower, text-focused layout
 * suited to long-form legal/policy content.
 *
 * @package turningpages
 */

get_header();
?>

<main class="content">
    <div class="container-legal">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="container-content">
                <div class="page-header">
                    <h1><?php the_title(); ?></h1>
                </div>
                <div class="page-text">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>