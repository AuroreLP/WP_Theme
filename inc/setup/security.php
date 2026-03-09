<?php
/**
 * Theme Security Hardening
 *
 * Applies security-oriented restrictions and HTTP headers at the theme level.
 * These complement (not replace) server-level security and security plugins
 * like Solid Security and Really Simple Security.
 *
 * What belongs HERE (theme level):
 * - Removing unnecessary WordPress meta tags from <head>
 * - HTTP security headers
 * - Upload restrictions
 * - REST API endpoint restrictions
 *
 * What belongs in wp-config.php (runs earlier, more reliable):
 * - DISALLOW_FILE_EDIT
 * - Database credentials, salts, debug flags
 *
 * What belongs at server level (Apache/Nginx/LiteSpeed):
 * - CSP (Content Security Policy) headers
 * - HSTS (Strict-Transport-Security)
 * - Rate limiting
 *
 * @package turningpages
 */


/* =========================================================================
 * 1. HIDE WORDPRESS VERSION
 * ========================================================================= */

/**
 * Remove the <meta name="generator" content="WordPress X.X"> tag.
 * This tag broadcasts the exact WP version, making it easier for
 * attackers to target known vulnerabilities for that version.
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Remove WP version from RSS feeds.
 */
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Remove version query string from CORE WordPress assets only.
 *
 * IMPORTANT: The previous version stripped ?ver= from ALL scripts and styles.
 * This broke the filemtime()-based cache busting in enqueue.php — visitors
 * would get stale CSS/JS after theme updates because the browser had no way
 * to know the file changed.
 *
 * This revised version only strips the version from WordPress core assets
 * (those served from wp-includes/ and wp-admin/). Theme and plugin assets
 * keep their version parameter for proper cache invalidation.
 *
 * The security benefit of hiding versions is minimal — an attacker can
 * determine WP version through many other signals. Cache busting, on the
 * other hand, directly impacts user experience.
 */
function tp_remove_version_from_core_assets( $src ) {
    if ( $src && strpos( $src, 'ver=' ) !== false ) {
        // Only strip version from WP core assets
        if ( strpos( $src, 'wp-includes/' ) !== false || strpos( $src, 'wp-admin/' ) !== false ) {
            $src = remove_query_arg( 'ver', $src );
        }
    }
    return $src;
}
add_filter( 'style_loader_src', 'tp_remove_version_from_core_assets', 9999 );
add_filter( 'script_loader_src', 'tp_remove_version_from_core_assets', 9999 );


/* =========================================================================
 * 2. CLEAN UP WP_HEAD
 * ========================================================================= */

/**
 * Remove unnecessary meta tags and links from <head>.
 *
 * - rsd_link: Really Simple Discovery — used by external blog editors
 *   (like Windows Live Writer). Not needed for a modern site.
 * - wlwmanifest_link: Windows Live Writer manifest — same as above.
 * - wp_shortlink_wp_head: Outputs <link rel="shortlink"> — redundant
 *   with canonical URLs managed by Rank Math.
 * - rest_output_link_wp_head: Outputs <link rel="api"> — exposes the
 *   REST API discovery URL. Not needed in <head>.
 * - wp_oembed_add_discovery_links: oEmbed discovery links — only needed
 *   if you want other sites to embed your posts as rich cards.
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );


/* =========================================================================
 * 3. DISABLE XML-RPC
 * ========================================================================= */

/**
 * XML-RPC is a legacy protocol for remote publishing (Blogger API, etc.).
 * It's a common target for brute-force and DDoS amplification attacks.
 * Disabling it has no downside for a modern WordPress site — the REST API
 * handles all remote communication now.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );


/* =========================================================================
 * 4. OBSCURE LOGIN ERRORS
 * ========================================================================= */

/**
 * Replace detailed login error messages with a generic one.
 *
 * By default, WordPress says "unknown username" or "incorrect password",
 * which tells attackers whether a username exists. This returns the same
 * message for all login failures.
 */
add_filter( 'login_errors', function () {
    return 'Identifiants incorrects.';
} );


/* =========================================================================
 * 5. HTTP SECURITY HEADERS
 * ========================================================================= */

/**
 * Add security-related HTTP headers to every response.
 *
 * - X-Content-Type-Options: nosniff
 *   Prevents browsers from MIME-sniffing a response away from the declared
 *   Content-Type. Stops attacks where a JS file is disguised as an image.
 *
 * - X-Frame-Options: SAMEORIGIN
 *   Prevents the site from being embedded in <iframe> on other domains.
 *   Protects against clickjacking attacks.
 *
 * - Referrer-Policy: strict-origin-when-cross-origin
 *   Sends the full URL as referrer for same-origin requests, but only
 *   the origin (domain) for cross-origin requests. Balances analytics
 *   needs with privacy.
 *
 * NOTE: X-XSS-Protection was removed — it was deprecated by all major
 * browsers in 2019 (Chrome 78+). It can actually introduce vulnerabilities
 * in some edge cases. Modern protection comes from Content-Security-Policy
 * headers, which should be configured at the server level (Apache/LiteSpeed).
 *
 * NOTE: These headers may duplicate what Solid Security or Really Simple
 * Security already set. Check your plugin settings to avoid conflicts.
 * Duplicate headers are usually harmless but messy.
 */
add_action( 'send_headers', 'tp_add_security_headers' );
function tp_add_security_headers() {
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
}


/* =========================================================================
 * 6. UPLOAD RESTRICTIONS
 * ========================================================================= */

/**
 * Remove dangerous file types from the allowed upload MIME list.
 *
 * WordPress already blocks most of these by default, but this acts as
 * a defense-in-depth layer. Even if a plugin re-enables a dangerous type,
 * this filter will strip it back out.
 */
add_filter( 'upload_mimes', 'tp_restrict_upload_mimes' );
function tp_restrict_upload_mimes( $mimes ) {
    $dangerous = array(
        'exe', 'php', 'phtml', 'php3', 'php4', 'php5', 'php6', 'php7',
        'pht', 'phps', 'pl', 'py', 'jsp', 'asp', 'sh', 'cgi', 'bat',
    );
    foreach ( $dangerous as $ext ) {
        unset( $mimes[ $ext ] );
    }
    return $mimes;
}

/**
 * Verify uploaded files by checking both extension AND real MIME type.
 *
 * The previous version only checked the extension, which means a PHP file
 * renamed to .jpg would pass. This version:
 * 1. Checks the extension against a whitelist
 * 2. Uses finfo to read the actual file contents and determine the real MIME
 * 3. Verifies that the real MIME matches what's expected for that extension
 *
 * This prevents double-extension attacks (malware.php.jpg) and MIME spoofing.
 */
add_filter( 'wp_handle_upload_prefilter', 'tp_verify_upload_file_type' );
function tp_verify_upload_file_type( $file ) {

    // Map of allowed extensions to their valid MIME types
    $allowed = array(
        'jpg'  => array( 'image/jpeg' ),
        'jpeg' => array( 'image/jpeg' ),
        'png'  => array( 'image/png' ),
        'gif'  => array( 'image/gif' ),
        'webp' => array( 'image/webp' ),
        'svg'  => array( 'image/svg+xml' ),
        'pdf'  => array( 'application/pdf' ),
        'doc'  => array( 'application/msword' ),
        'docx' => array( 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ),
        'xls'  => array( 'application/vnd.ms-excel' ),
        'xlsx' => array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ),
        'ppt'  => array( 'application/vnd.ms-powerpoint' ),
        'pptx' => array( 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ),
        'zip'  => array( 'application/zip', 'application/x-zip-compressed' ),
        'mp3'  => array( 'audio/mpeg', 'audio/mp3' ),
        'mp4'  => array( 'video/mp4' ),
        'avi'  => array( 'video/avi', 'video/x-msvideo' ),
        'mov'  => array( 'video/quicktime' ),
    );

    $ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

    // Step 1: Check extension against whitelist
    if ( ! isset( $allowed[ $ext ] ) ) {
        $file['error'] = 'Type de fichier non autorisé pour des raisons de sécurité.';
        return $file;
    }

    // Step 2: Verify real MIME type matches expected MIME for this extension
    if ( function_exists( 'finfo_open' ) ) {
        $finfo     = finfo_open( FILEINFO_MIME_TYPE );
        $real_mime = finfo_file( $finfo, $file['tmp_name'] );
        finfo_close( $finfo );

        if ( ! in_array( $real_mime, $allowed[ $ext ], true ) ) {
            $file['error'] = sprintf(
                'Le contenu du fichier ne correspond pas à son extension (.%s). Upload bloqué.',
                esc_html( $ext )
            );
        }
    }

    return $file;
}


/* =========================================================================
 * 7. COOKIE SECURITY
 * ========================================================================= */

/**
 * Harden PHP session cookies.
 *
 * - cookie_httponly: Prevents JavaScript from reading session cookies (XSS)
 * - cookie_secure: Only send cookies over HTTPS
 * - use_only_cookies: Prevents session fixation via URL parameters
 *
 * NOTE: cookie_secure=1 means cookies won't be sent over plain HTTP.
 * If you develop locally without HTTPS, sessions/cookies may not work.
 * Consider wrapping this in an is_ssl() check or setting it only in
 * production (wp-config.php is a better place for this).
 */
if ( is_ssl() ) {
    @ini_set( 'session.cookie_httponly', 1 );
    @ini_set( 'session.cookie_secure', 1 );
    @ini_set( 'session.use_only_cookies', 1 );
}


/* =========================================================================
 * 8. AUTO-UPDATES
 * ========================================================================= */

/**
 * Enable automatic updates for plugins and themes.
 *
 * Keeps the site patched against known vulnerabilities without manual
 * intervention. Trade-off: an update could break something without warning.
 *
 * For a personal blog, the security benefit outweighs the risk.
 * For a client or business site, consider a staging-first workflow instead.
 */
add_filter( 'auto_update_plugin', '__return_true' );
add_filter( 'auto_update_theme', '__return_true' );


/* =========================================================================
 * 9. RESTRICT REST API — Hide User Endpoints
 * ========================================================================= */

/**
 * Remove the /wp/v2/users endpoints from the REST API.
 *
 * By default, anyone can query /wp-json/wp/v2/users to get a list of
 * usernames. Combined with wp-login.php, this gives attackers half the
 * credentials they need. Removing these endpoints hides user enumeration.
 *
 * This does NOT disable the REST API entirely — it's still needed for
 * Gutenberg, CF7, and other plugins. Only user-related routes are removed.
 */
add_filter( 'rest_endpoints', function ( $endpoints ) {
    unset( $endpoints['/wp/v2/users'] );
    unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    return $endpoints;
} );


/* =========================================================================
 * REMINDER: Add to wp-config.php (not here)
 * ========================================================================= */

/**
 * The following constant should be in wp-config.php, not in the theme:
 *
 *   define('DISALLOW_FILE_EDIT', true);
 *
 * It disables the built-in Theme Editor and Plugin Editor in the admin.
 * Placing it in wp-config.php ensures it loads before any theme or plugin
 * code, making it impossible to bypass.
 *
 * If it's already in wp-config.php, the define() block that was here
 * previously was redundant. If it's not, add it there now.
 */
