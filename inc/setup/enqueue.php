<?php
// Enregistrement des styles
function turningpages_enqueue_styles() {
    // Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Cardo:ital,wght@0,400;0,700;1,400&display=swap', array(), null);
    
    // CSS principal (variables, reset, global)
    wp_enqueue_style('turningpages-style', get_stylesheet_uri());
    
    // CSS modulaires (chargés sur toutes les pages)
    $css_files = array(
        'navigation' => '/assets/css/components/navigation.css',
        'components' => '/assets/css/components/components.css',
        'spoilers' => '/assets/css/components/spoilers.css',
        'posts' => '/assets/css/pages/posts.css',
        'singles' => '/assets/css/layouts/singles.css',
        'comments' => '/assets/css/components/comments.css',
        'bilan' => '/assets/css/layouts/bilan.css'
    );
    
    foreach ($css_files as $handle => $file) {
        $file_path = get_template_directory() . '/' . $file;
        if (file_exists($file_path)) {
            wp_enqueue_style(
                'turningpages-' . $handle,
                get_template_directory_uri() . '/' . $file,
                array('turningpages-style'), // Dépendance au style principal
                filemtime($file_path)
            );
        }
    }
    
    // Style pour les archives de taxonomies (chargé conditionnellement)
    if (is_tax(array('genre', 'theme', 'nationalite', 'auteur'))) {
        $taxonomy_file = get_template_directory() . '/assets/css/pages/taxonomy-archives.css';
        if (file_exists($taxonomy_file)) {
            wp_enqueue_style(
                'taxonomy-archives-style',
                get_template_directory_uri() . '/assets/css/pages/taxonomy-archives.css',
                array('turningpages-style'),
                filemtime($taxonomy_file)
            );
        }
    }

    // Style pour les archives de catégories et tags (articles)
    if (is_category() || is_tag()) {
        $cat_tag_file = get_template_directory() . '/assets/css/pages/tags_categories_archives.css';
        if (file_exists($cat_tag_file)) {
            wp_enqueue_style(
                'tags-categories-archives-style',
                get_template_directory_uri() . '/assets/css/pages/tags_categories_archives.css',
                array('turningpages-style'),
                filemtime($cat_tag_file)
            );
        }
    }

    // Style pour les résultats de recherche
    if (is_search()) {
        $search_file = get_template_directory() . '/assets/css/pages/search.css';
        if (file_exists($search_file)) {
            wp_enqueue_style(
                'search-style',
                get_template_directory_uri() . '/assets/css/pages/search.css',
                array('turningpages-style'),
                filemtime($search_file)
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'turningpages_enqueue_styles');

// Enregistrement des scripts
function turningpages_enqueue_scripts() {
    // Ionicons
    wp_enqueue_script('ionicons-esm', 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js', array(), null, true);
    add_filter('script_loader_tag', 'add_type_module_to_ionicons', 10, 3);
    
    wp_enqueue_script('ionicons-nomodule', 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js', array(), null, true);

    // Script principal
    wp_enqueue_script(
        'turningpages-app',
        get_template_directory_uri() . '/assets/js/app.js',
        array('jquery'), // dépendance
        null,
        true // chargé dans le footer
    );

        // Filtres ET pagination pour la page chroniques
    if (is_page_template('page-chroniques.php')) {
        wp_enqueue_script(
            'filter-chroniques-script',
            get_template_directory_uri() . '/assets/js/modules/filter-chroniques.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/modules/filter-chroniques.js'),
            true
        );
    } elseif (is_page_template('page-articles.php')) {
        wp_enqueue_script(
            'filter-articles-script',
            get_template_directory_uri() . '/assets/js/modules/filter-articles.js',
            array(),
            filemtime(get_template_directory() . '/assets/js/modules/filter-articles.js'),
            true
        );
    }
    // Pagination simple pour les autres pages (accueil, archives, etc.)
    elseif (is_home() || is_front_page() || is_archive()) {
        wp_enqueue_script(
            'pagination-script',
            get_template_directory_uri() . '/assets/js/modules/pagination.js',
            array(),
            '1.0',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'turningpages_enqueue_scripts');

// Fonction pour ajouter les attributs module et nomodule
function add_type_module_to_ionicons($tag, $handle, $src) {
    if ('ionicons-esm' === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>' . "\n";
    }
    if ('ionicons-nomodule' === $handle) {
        $tag = '<script nomodule src="' . esc_url($src) . '"></script>' . "\n";
    }
    return $tag;
}

/**
 * Enqueue scripts pour les bilans trimestriels
 */
function enqueue_bilan_scripts() {
    if (is_singular('post')) {
        global $post;
        $is_bilan = false;
        
        // Vérifier si c'est un bilan
        $categories = get_the_category($post->ID);
        foreach ($categories as $category) {
            if ($category->slug === 'bilan') {
                $is_bilan = true;
                break;
            }
        }
        
        if ($is_bilan) {
            // Récupérer les stats ICI
            $mois_1 = get_field('mois_1', $post->ID);
            $mois_2 = get_field('mois_2', $post->ID);
            $mois_3 = get_field('mois_3', $post->ID);
            $mois_trimestre = array_filter(array($mois_1, $mois_2, $mois_3));

            // Chart.js
            wp_enqueue_script(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
                array(),
                '4.4.0',
                true
            );
            
            // Script personnalisé
            wp_enqueue_script(
                'bilan-charts',
                get_template_directory_uri() . '/assets/js/modules/bilan-charts.js',
                array('chartjs'),
                filemtime(get_template_directory() . '/assets/js/modules/bilan-charts.js'),
                true
            );

            // Script comments
            wp_enqueue_script(
                'comments-script',
                get_template_directory_uri() . '/assets/js/modules/comments.js',
                array(),
                '1.0.0',
                true
            );


            // Passer les données au JavaScript
            if (!empty($mois_trimestre) && function_exists('get_trimestre_stats')) {
                $stats = get_trimestre_stats($mois_trimestre);
                
                wp_localize_script('bilan-charts', 'bilanData', array(
                    'nationalites' => $stats['nationalites'] ?? array(),
                    'genres' => $stats['genres'] ?? array(),
                    'auteurs' => array(
                        'femmes' => $stats['auteurs_femmes'] ?? 0,
                        'hommes' => $stats['auteurs_hommes'] ?? 0,
                        'total' => $stats['auteurs_total'] ?? 0
                    )
                ));
            }

        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_bilan_scripts');

?>