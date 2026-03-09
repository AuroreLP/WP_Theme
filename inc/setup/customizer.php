<?php
/**
 * Theme Customizer — Social Media Settings
 *
 * Registers a "Réseaux sociaux" section in the WordPress Customizer
 * (Appearance > Customize) where social media URLs can be configured.
 *
 * These URLs are read in the front end by the social-links template part:
 *   get_template_part('inc/template-parts/components/social-links')
 *
 * Each URL is stored as a theme_mod and sanitized with esc_url_raw(),
 * which is the correct sanitizer for URLs being saved to the database
 * (as opposed to esc_url() which is for output in HTML).
 *
 * To add a new social network:
 * 1. Add an entry to the $social_networks array below
 * 2. Add a matching entry in inc/template-parts/components/social-links.php
 *
 * @package turningpages
 */

add_action( 'customize_register', 'tp_customize_register' );
function tp_customize_register( $wp_customize ) {

    // Register the social links section in the Customizer panel
    $wp_customize->add_section( 'social_links', array(
        'title'    => 'Réseaux sociaux',
        'priority' => 30,
    ) );

    /**
     * Social networks configuration.
     *
     * Each entry creates a Customizer setting + control pair.
     * The 'mod' key must match the theme_mod name used in social-links.php.
     */
    $social_networks = array(
        array(
            'mod'   => 'youtube_url',
            'label' => 'URL YouTube',
        ),
        array(
            'mod'   => 'instagram_url',
            'label' => 'URL Instagram',
        ),
        array(
            'mod'   => 'mastodon_url',
            'label' => 'URL Mastodon',
        ),
    );

    foreach ( $social_networks as $network ) {
        $wp_customize->add_setting( $network['mod'], array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ) );

        $wp_customize->add_control( $network['mod'], array(
            'label'   => $network['label'],
            'section' => 'social_links',
            'type'    => 'url',
        ) );
    }
}
