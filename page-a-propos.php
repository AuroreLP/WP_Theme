<?php 
/*
Template Name: À propos
*/
get_header(); 
?>

<main class="content">
    <section class="container">
        <div class="heading"><h1><?php echo wp_kses_post(get_field('about_title_section')); ?></h1></div>
        <div class="columns">
            <?php
            if (have_posts()) : 
                while (have_posts()) : the_post();
                    the_content();
                endwhile;
            endif;
            ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>