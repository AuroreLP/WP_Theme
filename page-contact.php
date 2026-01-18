<?php 
/*
Template Name: Contact
*/
get_header(); 
?>

<main class="content">
    <section class="container">
        <div class="heading"><h1><?php echo wp_kses_post(get_field('contact_title_section')); ?></h1></div>
        <div class="container-content contact">
            <?php 
            echo do_shortcode('[contact-form-7 id="5501d4c" title="Formulaire de contact 1"]')
            ;?>
        </div>
    </section>
</main>

</section> <!-- Fermeture de .blog -->

<?php get_footer(); ?>