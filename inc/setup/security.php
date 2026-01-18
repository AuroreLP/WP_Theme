<?php
/**
 * Configuration de sécurité du thème
 */

// ==========================================
// MASQUER LA VERSION DE WORDPRESS
// ==========================================

// Supprimer la version du header HTML
remove_action('wp_head', 'wp_generator');

// Supprimer la version des CSS/JS
function remove_version_from_assets($src) {
    if ($src && strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'remove_version_from_assets', 9999);
add_filter('script_loader_src', 'remove_version_from_assets', 9999);

// Supprimer la version des flux RSS
add_filter('the_generator', '__return_empty_string');

// ==========================================
// AUTRES MESURES DE SÉCURITÉ
// ==========================================

// Désactiver l'éditeur de fichiers WordPress (important !)
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// Désactiver XML-RPC (prévient les attaques brute force)
add_filter('xmlrpc_enabled', '__return_false');

// Supprimer le lien RSD du header
remove_action('wp_head', 'rsd_link');

// Supprimer le lien Windows Live Writer
remove_action('wp_head', 'wlwmanifest_link');

// Supprimer les liens shortlink
remove_action('wp_head', 'wp_shortlink_wp_head');

// Supprimer les API REST des headers (optionnel)
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');

// Masquer les erreurs de connexion (ne pas donner d'indices aux attaquants)
add_filter('login_errors', function() {
    return 'Identifiants incorrects.';
});

// Ajouter des en-têtes de sécurité HTTP
function add_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
add_action('send_headers', 'add_security_headers');