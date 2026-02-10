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

    <!-- Hamburger reste global -->
    <div class="menu-toggle">
        <div class="hamburger">
            <span></span>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header">
        <!-- Logo / Hero -->
        <div class="hero">
            <a href="<?php echo home_url('/'); ?>" class="site-logo">
                <img 
                    id="site-logo"
                    src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logos/light_logo.png"
                    alt="Logo Turning Pages"
                >
            </a>

            <div class="search-mobile">
                <?php get_search_form(); ?>
            </div>
        </div>

        <!-- Navigation -->
        <?php get_template_part('inc/template-parts/navigation/nav'); ?>

    </header>