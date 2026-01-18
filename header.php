   <?php 
   global $template;
   echo '<!-- Template: ' . basename($template) . ' -->';
   ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logos/faviconturningpages.png">
    <title><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<section class="blog">
    <!-- Navigation -->
    <div class="menu-toggle">
        <div class="hamburger">
            <span></span>
        </div>
    </div>
    <!-- Hero -->
    <div class="hero">
        <a href="<?php echo home_url('/'); ?>" class="logo">
            <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logos/logo_pensees_livresques.svg" alt="logo Pensées livresques">
        </a>
        <p class="home-subtitle">Le plaisir d'apprendre en lisant</p>
        <div class="search-mobile">
            <?php get_search_form(); ?>
        </div>
    </div>

<?php get_template_part('inc/template-parts/navigation/nav'); ?>
    
