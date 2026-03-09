<?php
/**
 * Asset Enqueuing — Styles & Scripts
 *
 * Centralizes all CSS and JS registration for the theme.
 * WordPress loads assets via wp_enqueue_style / wp_enqueue_script, which:
 * - Manages dependencies (load order)
 * - Handles cache busting via version parameter
 * - Outputs tags in the correct location (<head> or before </body>)
 * - Avoids duplicate loading if multiple components request the same asset
 *
 * Three hooks are registered on 'wp_enqueue_scripts':
 * 1. turningpages_enqueue_styles  — all CSS (global + conditional)
 * 2. turningpages_enqueue_scripts — all JS (global + conditional)
 * 3. enqueue_bilan_scripts        — Chart.js + data for quarterly reports
 *
 * @package turningpages
 */

/* =========================================================================
 * HELPER: Cache-busting version from file modification time
 * ========================================================================= */

/**
 * Return filemtime-based version string for a theme asset.
 *
 * Using file modification time as the version means the browser cache
 * is automatically invalidated whenever the file changes — no need to
 * manually bump version numbers.
 *
 * @param  string      $relative_path  Path relative to the theme root.
 * @return string|null                 File mod time as string, or null.
 */
function tp_asset_version( $relative_path ) {
    $file = get_template_directory() . '/' . $relative_path;
    return file_exists( $file ) ? (string) filemtime( $file ) : null;
}


/* =========================================================================
 * 1. STYLESHEETS
 * ========================================================================= */

add_action( 'wp_enqueue_scripts', 'turningpages_enqueue_styles' );
function turningpages_enqueue_styles() {

    /**
     * Google Fonts — Montserrat (headings) + Cardo (body/serif).
     *
     * Version is null to prevent ?ver= which would break CDN caching.
     * display=swap keeps text visible while fonts load (LCP-friendly).
     */
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Cardo:ital,wght@0,400;0,700;1,400&display=swap',
        array(),
        null
    );

    /**
     * Main stylesheet (style.css at theme root).
     * Contains CSS variables, reset, and global rules.
     */
    wp_enqueue_style(
        'turningpages-style',
        get_stylesheet_uri(),
        array(),
        tp_asset_version( 'style.css' )
    );

    /**
     * Modular CSS — loaded on every page.
     * Each file handles a specific UI concern. To add a new global
     * CSS file, just add an entry to this array.
     */
    $css_files = array(
        'navigation'     => 'assets/css/components/navigation.css',
        'components'     => 'assets/css/components/components.css',
        'spoilers'       => 'assets/css/components/spoilers.css',
        'posts'          => 'assets/css/pages/posts.css',
        'singles'        => 'assets/css/layouts/singles.css',
        'comments'       => 'assets/css/components/comments.css',
        'bilan'          => 'assets/css/layouts/bilan.css',
        'theme-switcher' => 'assets/css/components/theme-switcher.css',
    );

    foreach ( $css_files as $handle => $file ) {
        $file_path = get_template_directory() . '/' . $file;
        if ( file_exists( $file_path ) ) {
            wp_enqueue_style(
                'turningpages-' . $handle,
                get_template_directory_uri() . '/' . $file,
                array( 'turningpages-style' ),
                filemtime( $file_path )
            );
        }
    }

    /**
     * Conditional CSS — loaded only on matching page types
     * to keep page weight down.
     */

    // Taxonomy archives + artistes listing page
    if ( is_tax( array( 'genre', 'theme', 'nationalite', 'role' ) ) || is_page_template( 'page-artistes.php' ) ) {
        $path = 'assets/css/pages/taxonomy-archives.css';
        if ( file_exists( get_template_directory() . '/' . $path ) ) {
            wp_enqueue_style(
                'taxonomy-archives-style',
                get_template_directory_uri() . '/' . $path,
                array( 'turningpages-style' ),
                tp_asset_version( $path )
            );
        }
    }

    // Category and tag archives (articles)
    if ( is_category() || is_tag() ) {
        $path = 'assets/css/pages/tags_categories_archives.css';
        if ( file_exists( get_template_directory() . '/' . $path ) ) {
            wp_enqueue_style(
                'tags-categories-archives-style',
                get_template_directory_uri() . '/' . $path,
                array( 'turningpages-style' ),
                tp_asset_version( $path )
            );
        }
    }

    // Search results page
    if ( is_search() ) {
        $path = 'assets/css/pages/search.css';
        if ( file_exists( get_template_directory() . '/' . $path ) ) {
            wp_enqueue_style(
                'search-style',
                get_template_directory_uri() . '/' . $path,
                array( 'turningpages-style' ),
                tp_asset_version( $path )
            );
        }
    }
}


/* =========================================================================
 * 2. SCRIPTS
 * ========================================================================= */

add_action( 'wp_enqueue_scripts', 'turningpages_enqueue_scripts' );
function turningpages_enqueue_scripts() {

    /**
     * Ionicons — icon library, ES module with nomodule fallback.
     *
     * PERFORMANCE NOTE: unpkg.com = extra DNS lookup + TLS handshake.
     * Consider self-hosting in assets/js/vendor/ if latency matters.
     */
    wp_enqueue_script( 'ionicons-esm', 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js', array(), null, true );
    wp_enqueue_script( 'ionicons-nomodule', 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js', array(), null, true );
    add_filter( 'script_loader_tag', 'add_type_module_to_ionicons', 10, 3 );

    /**
     * Main application script — vanilla JS, no dependencies.
     *
     * Handles global UI interactions (mobile menu toggle, etc.).
     * jQuery dependency was removed since app.js uses only native APIs.
     */
    wp_enqueue_script(
        'turningpages-app',
        get_template_directory_uri() . '/assets/js/app.js',
        array(),
        tp_asset_version( 'assets/js/app.js' ),
        true
    );

    /**
     * Theme switcher — color scheme toggling + logo swap.
     *
     * Receives two localized data objects:
     * - themeData:  general theme path
     * - themeLogos: per-scheme logo URLs from logo-manager.php
     */
    wp_enqueue_script(
        'theme-switcher',
        get_template_directory_uri() . '/assets/js/modules/theme-switcher.js',
        array(),
        tp_asset_version( 'assets/js/modules/theme-switcher.js' ),
        true
    );

    wp_localize_script( 'theme-switcher', 'themeData', array(
        'themePath' => get_template_directory_uri(),
    ) );

    /**
     * Logo URLs for the theme switcher.
     *
     * Uses tp_get_logo_url() (from logo-manager.php) which checks:
     * 1. New attachment-ID option → 2. Legacy URL option → 3. Hardcoded default
     */
    $logo_defaults = array(
        'theme-light' => get_template_directory_uri() . '/assets/images/logos/purple_logo.png',
        'theme-dark'  => get_template_directory_uri() . '/assets/images/logos/light_logo.png',
        'theme-green' => get_template_directory_uri() . '/assets/images/logos/green_logo.png',
    );

    $logos = array();
    foreach ( array( 'light', 'dark', 'green' ) as $key ) {
        $url = function_exists( 'tp_get_logo_url' ) ? tp_get_logo_url( $key, 'full' ) : '';
        $logos[ "theme-{$key}" ] = $url ? $url : $logo_defaults[ "theme-{$key}" ];
    }

    wp_localize_script( 'theme-switcher', 'themeLogos', $logos );

    /**
     * Force jQuery on pages where Contact Form 7 is present.
     *
     * The "Contact Form Entries" (CFDB7) plugin injects an inline script
     * that depends on jQuery but doesn't declare it as a WP dependency.
     * Without this, jQuery is not loaded (since our theme doesn't need it)
     * and CFDB7's inline script throws "jQuery is not defined".
     *
     * This only loads jQuery on pages that actually contain a CF7 shortcode,
     * keeping other pages jQuery-free.
     */
    global $post;
    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'contact-form-7' ) ) {
        wp_enqueue_script( 'jquery' );
    }

    /** UI filter dropdowns */
    wp_enqueue_script(
        'ui-filters',
        get_template_directory_uri() . '/assets/js/modules/ui-filters.js',
        array(),
        tp_asset_version( 'assets/js/modules/ui-filters.js' ),
        true
    );

    /** Search overlay open/close */
    wp_enqueue_script(
        'search-overlay',
        get_template_directory_uri() . '/assets/js/modules/search-overlay.js',
        array(),
        tp_asset_version( 'assets/js/modules/search-overlay.js' ),
        true
    );

    /**
     * Comments interaction script.
     *
     * Loaded on all single views: articles (post) and chroniques (CPT).
     * Handles comment form behavior, reply threading, etc.
     */
    if ( is_singular( array( 'post', 'chroniques' ) ) ) {
        wp_enqueue_script(
            'comments-script',
            get_template_directory_uri() . '/assets/js/modules/comments.js',
            array(),
            tp_asset_version( 'assets/js/modules/comments.js' ),
            true
        );
    }

    /**
     * Conditional page-specific filter/pagination scripts.
     *
     * Each listing page has its own filter + pagination logic.
     * Other archive pages get a simpler pagination-only script.
     * elseif ensures only one loads per page.
     */
    if ( is_page_template( 'page-chroniques.php' ) ) {
        wp_enqueue_script(
            'filter-chroniques-script',
            get_template_directory_uri() . '/assets/js/modules/filter-chroniques.js',
            array(),
            tp_asset_version( 'assets/js/modules/filter-chroniques.js' ),
            true
        );
    } elseif ( is_page_template( 'page-articles.php' ) ) {
        wp_enqueue_script(
            'filter-articles-script',
            get_template_directory_uri() . '/assets/js/modules/filter-articles.js',
            array(),
            tp_asset_version( 'assets/js/modules/filter-articles.js' ),
            true
        );
    } elseif ( is_page_template( 'page-artistes.php' ) ) {
        wp_enqueue_script(
            'filter-artistes-script',
            get_template_directory_uri() . '/assets/js/modules/filter-artistes.js',
            array(),
            tp_asset_version( 'assets/js/modules/filter-artistes.js' ),
            true
        );
    } elseif ( is_home() || is_front_page() || is_archive() ) {
        wp_enqueue_script(
            'pagination-script',
            get_template_directory_uri() . '/assets/js/modules/pagination.js',
            array(),
            tp_asset_version( 'assets/js/modules/pagination.js' ),
            true
        );
    }
}


/* =========================================================================
 * 3. SCRIPT TAG MODIFICATIONS
 * ========================================================================= */

/**
 * Add type="module" and nomodule attributes to Ionicons scripts.
 *
 * WordPress doesn't natively support ES module scripts, so we filter
 * the <script> tag output. Modern browsers load the module version;
 * legacy browsers fall back to the classic version.
 */
function add_type_module_to_ionicons( $tag, $handle, $src ) {
    if ( 'ionicons-esm' === $handle ) {
        return '<script type="module" src="' . esc_url( $src ) . '"></script>' . "\n";
    }
    if ( 'ionicons-nomodule' === $handle ) {
        return '<script nomodule src="' . esc_url( $src ) . '"></script>' . "\n";
    }
    return $tag;
}


/* =========================================================================
 * 4. QUARTERLY REPORT (BILAN) — Chart.js + Data
 * ========================================================================= */

/**
 * Conditionally load Chart.js and bilan stats on quarterly report posts.
 *
 * Only fires on single posts in the 'bilan' category to avoid loading
 * Chart.js (~200kb) on every page. Workflow:
 * 1. Check for 'bilan' category
 * 2. Load Chart.js from CDN
 * 3. Load custom chart rendering script
 * 4. Read ACF month fields and compute stats via get_trimestre_stats()
 * 5. Pass data to JS via wp_localize_script
 */
add_action( 'wp_enqueue_scripts', 'enqueue_bilan_scripts' );
function enqueue_bilan_scripts() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    if ( ! has_category( 'bilan' ) ) {
        return;
    }

    global $post;

    $mois_1 = get_field( 'mois_1', $post->ID );
    $mois_2 = get_field( 'mois_2', $post->ID );
    $mois_3 = get_field( 'mois_3', $post->ID );
    $mois_trimestre = array_filter( array( $mois_1, $mois_2, $mois_3 ) );

    wp_enqueue_script(
        'chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
        array(),
        '4.4.0',
        true
    );

    wp_enqueue_script(
        'bilan-charts',
        get_template_directory_uri() . '/assets/js/modules/bilan-charts.js',
        array( 'chartjs' ),
        tp_asset_version( 'assets/js/modules/bilan-charts.js' ),
        true
    );

    if ( ! empty( $mois_trimestre ) && function_exists( 'get_trimestre_stats' ) ) {
        $stats = get_trimestre_stats( $mois_trimestre );

        wp_localize_script( 'bilan-charts', 'bilanData', array(
            'nationalites' => $stats['nationalites'] ?? array(),
            'genres'       => $stats['genres'] ?? array(),
            'auteurs'      => array(
                'femmes' => $stats['auteurs_femmes'] ?? 0,
                'hommes' => $stats['auteurs_hommes'] ?? 0,
                'total'  => $stats['auteurs_total'] ?? 0,
            ),
        ) );
    }
}
