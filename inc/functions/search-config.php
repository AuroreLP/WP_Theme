<?php
/**
 * Configuration et filtres de recherche
 */

// Inclure les custom post types dans la recherche
function theme_search_filter($query) {
    // Vérifier que c'est bien la requête principale de recherche
    if ($query->is_search && !is_admin() && $query->is_main_query()) {
        // Inclure UNIQUEMENT les posts et chroniques (PAS les pages)
        $query->set('post_type', array('post', 'chroniques'));
        
        // Exclure les brouillons
        $query->set('post_status', 'publish');
        
        // Limiter le nombre de résultats par page
        $query->set('posts_per_page', 12);
    }
    return $query;
}
add_filter('pre_get_posts', 'theme_search_filter');

// Forcer DISTINCT pour éviter les doublons
function search_distinct($distinct) {
    if (is_search()) {
        return 'DISTINCT';
    }
    return $distinct;
}
add_filter('posts_distinct', 'search_distinct');