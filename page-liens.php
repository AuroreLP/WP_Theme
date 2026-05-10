<?php
/**
 * Template Name: Page Liens
 *
 * Standalone links page (Linktree-style) — intended to be shared as a
 * bio link on social media. Renders without the normal site header/nav
 * so visitors see a clean, focused landing page.
 *
 * Content is managed in WordPress admin:
 * - Page title    → site name (get_bloginfo)
 * - Page content  → "À propos" mini-bio (Gutenberg editor)
 * - Social links  → Appearance > Customize > Réseaux sociaux
 *
 * @package turningpages
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function () {
            var t = localStorage.getItem( 'user-theme' ) || 'theme-light';
            document.documentElement.classList.add( t );
        })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'liens-body' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'inc/template-parts/page-liens' ); ?>

<?php wp_footer(); ?>
</body>
</html>
