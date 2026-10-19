<?php
/**
 * Theme Setup — Core Supports, Menus & Query Modifications
 *
 * Registers WordPress theme features, navigation menus,
 * and modifies default query behavior.
 *
 * @package turningpages
 */

/**
 * Hide the WordPress admin bar on the front end.
 *
 * This applies to ALL users, including administrators.
 * Useful for a clean front-end experience, but means you lose
 * quick-access links (Edit Post, Cache Purge, etc.) while browsing.
 * To show it for admins only, use:
 *   if ( ! current_user_can('manage_options') ) return false;
 */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Core theme setup — runs on 'after_setup_theme'.
 *
 * This is the standard place to declare what WordPress features
 * the theme supports. It fires early in the load process, before
 * init, so it's safe for theme support declarations.
 */
add_action( 'after_setup_theme', 'theme_setup' );
function theme_setup() {

    // Enable featured images (thumbnails) on posts and pages
    add_theme_support( 'post-thumbnails' );

    // Let WordPress manage the <title> tag in <head> automatically
    // (works with Rank Math to output SEO-optimized titles)
    add_theme_support( 'title-tag' );

    /**
     * Register navigation menu locations.
     *
     * After registration, menus are assigned in the admin:
     * Appearance > Menus > Menu Settings > Display location
     */
    register_nav_menus( array(
        'primary' => 'Menu Principal',
        'footer'  => 'Menu Footer',
    ) );

    /**
     * HTML5 markup support for WordPress-generated elements.
     * Outputs semantic HTML5 instead of legacy XHTML markup
     * for search forms, comments, galleries, etc.
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
}

/**
 * Limit the number of posts on the static front page.
 *
 * pre_get_posts modifies the main WordPress query BEFORE it runs.
 * This avoids a redundant secondary WP_Query in the template.
 *
 * Guards:
 * - is_main_query(): don't affect sidebar/widget queries
 * - !is_admin(): don't affect admin post lists
 * - is_front_page(): only target the front page
 */
add_action( 'pre_get_posts', 'custom_front_page_query' );
function custom_front_page_query( $query ) {
    if ( $query->is_main_query() && ! is_admin() && is_front_page() ) {
        $query->set( 'posts_per_page', 6 );
    }
}

/**
 * Auto-assign single-bilan.php template for posts in the "bilan" category.
 *
 * WordPress template hierarchy doesn't natively support category-based
 * single templates (only single-{post_type}.php and single-{slug}.php).
 * This filter manually overrides the template when a post belongs to
 * the 'bilan' category, enabling a distinct layout for quarterly reports.
 *
 * locate_template() checks both child and parent theme directories.
 */
add_filter( 'single_template', 'custom_single_template' );
function custom_single_template( $template ) {
    global $post;

    if ( is_single() && has_category( 'bilan', $post->ID ) ) {
        $bilan_template = locate_template( array( 'single-bilan.php' ) );
        if ( ! empty( $bilan_template ) ) {
            return $bilan_template;
        }
    }

    return $template;
}

/**
 * Force the Classic Editor for the 'artiste' CPT (managed by Pods).
 *
 * The Pods meta fields and the custom admin layout for artist profiles
 * are built for the Classic Editor. Gutenberg's block-based UI would
 * break the field arrangement and editing workflow.
 *
 * This only affects 'artiste' — all other post types keep Gutenberg.
 */
add_filter( 'use_block_editor_for_post_type', function ( $use_block_editor, $post_type ) {
    if ( 'artiste' === $post_type ) {
        return false;
    }
    return $use_block_editor;
}, 10, 2 );
