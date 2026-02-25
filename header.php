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

    <script>
        (function() {
            const savedTheme = localStorage.getItem('user-theme') || 'theme-light';
            document.documentElement.classList.add(savedTheme);
        })();
    </script>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<section class="blog">
    <div class="search-overlay">
        <button class="search-close" aria-label="Fermer la recherche">&times;</button>
        <div class="search">
            <?php get_search_form(); ?>
        </div>
    </div>

    <div class="theme-switcher">
        <button class="theme-toggle" aria-label="Changer le thème">
            <span id="current-theme-name">Lilac wine</span>
        </button>

        <div class="theme-menu">
            <button data-theme="theme-light">Lilac wine</button>
            <button data-theme="theme-dark">Purple rain</button>
            <button data-theme="theme-green">Green day</button>
        </div>
    </div>

    <!-- Réseaux sociaux - en haut à droite (au même niveau) -->
    <div class="social-links">
            <?php if( get_theme_mod('youtube_url') ): ?>
                <a href="<?php echo esc_url( get_theme_mod('youtube_url') ); ?>" target="_blank" rel="noopener noreferrer">
                    <ion-icon name="logo-youtube"></ion-icon>
                </a>
            <?php endif; ?>
            
            <?php if( get_theme_mod('instagram_url') ): ?>
                <a href="<?php echo esc_url( get_theme_mod('instagram_url') ); ?>" target="_blank" rel="noopener noreferrer">
                    <ion-icon name="logo-instagram"></ion-icon>
                </a>
            <?php endif; ?>
            
            <?php if( get_theme_mod('mastodon_url') ): ?>
                <a href="<?php echo esc_url( get_theme_mod('mastodon_url') ); ?>" target="_blank" rel="noopener noreferrer">
                    <ion-icon name="logo-mastodon"></ion-icon>
                </a>
            <?php endif; ?>
    </div>

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
        </div>

        <!-- Navigation -->
        <?php get_template_part('inc/template-parts/navigation/nav'); ?>

    </header>