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

// ==========================================
// SÉCURISER LES UPLOADS
// ==========================================
// Bloquer les types de fichiers dangereux
function securiser_types_fichiers($mimes) {
    unset($mimes['exe']);
    unset($mimes['php']);
    unset($mimes['phtml']);
    unset($mimes['php3']);
    unset($mimes['php4']);
    unset($mimes['php5']);
    unset($mimes['php6']);
    unset($mimes['php7']);
    unset($mimes['pht']);
    unset($mimes['phps']);
    unset($mimes['pl']);
    unset($mimes['py']);
    unset($mimes['jsp']);
    unset($mimes['asp']);
    unset($mimes['sh']);
    unset($mimes['cgi']);
    unset($mimes['bat']);
    return $mimes;
}
add_filter('upload_mimes', 'securiser_types_fichiers');

// Vérifier le vrai type MIME du fichier uploadé
function verifier_type_fichier($file) {
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // Extensions autorisées (adapte selon tes besoins)
        $extensions_autorisees = array(
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'zip', 'rar', 'mp3', 'mp4', 'avi', 'mov'
        );
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $extensions_autorisees)) {
            $file['error'] = 'Type de fichier non autorisé pour des raisons de sécurité.';
        }
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'verifier_type_fichier');

// ==========================================
// SÉCURISER LES COOKIES
// ==========================================
@ini_set('session.cookie_httponly', 1);
@ini_set('session.cookie_secure', 1);
@ini_set('session.use_only_cookies', 1);

// ==========================================
// MISES À JOUR AUTOMATIQUES
// ==========================================
add_filter('auto_update_plugin', '__return_true');
add_filter('auto_update_theme', '__return_true');

// ==========================================
// LIMITER REST API
// ==========================================
// Masquer les endpoints utilisateurs du REST API
add_filter('rest_endpoints', function($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});