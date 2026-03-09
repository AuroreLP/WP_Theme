<?php
/**
 * Header Template
 *
 * Opens the main HTML document and renders the top-level site structure:
 * debug info, <head>, theme switcher, social links, hamburger menu,
 * site logo, and primary navigation.
 *
 * NOTE: This file opens a <section class="blog"> wrapper that is closed
 * in footer.php. All page content sits between header and footer inside
 * this container. The CSS layout depends on this structure.
 *
 * @package turningpages
 */
?>

<?php
/**
 * Debug: output the active template filename as an HTML comment.
 * Conditioned on WP_DEBUG to avoid leaking internal file paths in production.
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) :
    global $template;
    echo '<!-- Template: ' . esc_html( basename( $template ) ) . ' -->';
endif;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    /**
     * Favicon is managed by the native WordPress Customizer:
     * Appearance > Customize > Site Identity > Site Icon.
     * It auto-generates all required sizes and injects the
     * appropriate <link> tags via wp_head() below.
     */
    ?>

    <?php
    /**
     * Inline theme initializer — runs before page render.
     *
     * Reads the saved color scheme from localStorage and applies it
     * to <html> immediately, preventing a flash of wrong theme on load.
     * Must stay inline in <head> (not enqueued) to execute before first paint.
     */
    ?>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('user-theme') || 'theme-light';
            document.documentElement.classList.add(savedTheme);
        })();
    </script>

    <?php
    /**
     * wp_head() — WordPress hook that outputs enqueued styles/scripts,
     * SEO meta tags (Rank Math), RSS feeds, canonical URLs, and plugin assets.
     */
    wp_head();
    ?>
</head>

<body <?php body_class(); ?>>
<?php
/**
 * wp_body_open() — fires right after <body>.
 * Used by plugins for tracking scripts, skip-to-content links, etc.
 */
wp_body_open();
?>

<?php /* ── Main site wrapper — closed in footer.php ── */ ?>
<section class="blog">

    <?php /* ── Search overlay (toggled via JS) ── */ ?>
    <div class="search-overlay">
        <button class="search-close" aria-label="Fermer la recherche">&times;</button>
        <div class="search">
            <?php get_search_form(); ?>
        </div>
    </div>

    <?php
    /**
     * Theme switcher — three color schemes.
     *
     * Theme names reference music tracks:
     * - "Lilac wine" (light)  — Jeff Buckley
     * - "Purple rain" (dark)  — Prince
     * - "Green day" (green)   — Green Day
     *
     * JS handler swaps a class on <html> and saves to localStorage.
     */
    ?>
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

    <?php /* ── Social links (header position, top-right) ── */ ?>
    <div class="social-links">
        <?php get_template_part( 'inc/template-parts/components/social-links' ); ?>
    </div>

    <?php /* ── Mobile hamburger menu trigger ── */ ?>
    <div class="menu-toggle">
        <div class="hamburger">
            <span></span>
        </div>
    </div>

    <?php /* ── Site header: logo + navigation ── */ ?>
    <header class="site-header">

        <?php
        /**
         * Logo / Hero area.
         *
         * The logo src is resolved dynamically via tp_get_logo_url():
         * 1. Checks the admin "Logos Thèmes" page (attachment ID option)
         * 2. Falls back to legacy URL-based option
         * 3. Falls back to the hardcoded default image in the theme
         *
         * This ensures the logo reflects what's configured in the admin
         * from the very first paint, before the theme-switcher JS takes
         * over to swap logos on color scheme changes.
         */
        $default_logo = get_template_directory_uri() . '/assets/images/logos/light_logo.png';
        $logo_url     = function_exists( 'tp_get_logo_url' ) ? tp_get_logo_url( 'light', 'full' ) : '';
        $logo_src     = $logo_url ? $logo_url : $default_logo;
        ?>

        <div class="hero">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo">
                <img
                    id="site-logo"
                    src="<?php echo esc_url( $logo_src ); ?>"
                    alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                    width="300"
                    height="100"
                >
            </a>
        </div>

        <?php
        /**
         * Primary navigation — loaded from inc/template-parts/navigation/nav.php
         */
        get_template_part( 'inc/template-parts/navigation/nav' );
        ?>

    </header>