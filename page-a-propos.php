<?php
/**
 * Template Name: À propos
 *
 * Static page template for the "About" section.
 * Title is pulled from an ACF field (about_title_section) to allow
 * styled HTML in the heading (e.g. <span> for color accents).
 * Body content comes from the standard Gutenberg editor.
 *
 * @package turningpages
 */

get_header();
?>

<main class="content">
    <section class="container">
        <div class="heading">
            <h1><?php echo wp_kses_post( get_field( 'about_title_section' ) ); ?></h1>
        </div>
        <div class="columns">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            endif;
            ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>