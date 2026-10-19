<?php
/**
 * Template Name: Contact
 *
 * Static page template for the contact form.
 * Title is pulled from an ACF field (contact_title_section).
 * The form itself is rendered by Contact Form 7 via its shortcode.
 *
 * NOTE: jQuery is conditionally loaded on this page (see enqueue.php)
 * because the Contact Form Entries (CFDB7) plugin injects an inline
 * script that depends on it.
 *
 * @package turningpages
 */

get_header();
?>

<main class="content">
    <section class="container">
        <div class="heading">
            <h1><?php echo wp_kses_post( get_field( 'contact_title_section' ) ); ?></h1>
        </div>
        <div class="container-content contact">
            <?php echo do_shortcode( '[contact-form-7 id="5501d4c" title="Formulaire de contact 1"]' ); ?>
        </div>
    </section>
</main>

<?php
/**
 * NOTE: A stray </section> tag was removed here.
 * The <section class="blog"> wrapper is opened in header.php and
 * closed in footer.php. Adding an extra </section> here broke
 * the HTML structure.
 */
?>

<?php get_footer(); ?>