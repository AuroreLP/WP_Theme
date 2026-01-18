<?php
/**
 * Configuration du thème - Supports et menus
 */

// Cacher la barre d'admin
add_filter('show_admin_bar', '__return_false');

// Configuration du thème
add_action('after_setup_theme', 'theme_setup');
function theme_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('title-tag');
    
    register_nav_menus(array(
        'primary' => 'Menu Principal',
        'footer' => 'Menu Footer'
    ));
    
    // Support HTML5
    add_theme_support('html5', array(
        'search-form', 
        'comment-form', 
        'comment-list', 
        'gallery', 
        'caption'
    ));
}

// Permettre la pagination sur la page d'accueil
add_action('pre_get_posts', 'custom_front_page_query');
function custom_front_page_query($query) {
    if ($query->is_main_query() && !is_admin() && is_front_page()) {
        $query->set('posts_per_page', 6);
    }
}

// Appliquer automatiquement un template selon la catégorie d'un article
add_filter('single_template', 'custom_single_template');
function custom_single_template($template) {
    global $post;
    
    // Vérifier si l'article est dans la catégorie "Bilan"
    if (is_single() && has_category('bilan', $post->ID)) {
        $new_template = locate_template(array('single-bilan.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    
    return $template;
}