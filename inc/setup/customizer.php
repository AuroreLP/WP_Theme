<?php
/**
 * Configuration du Customizer pour les réglages du thème
 */

function theme_customize_register($wp_customize) {
    // Section Réseaux sociaux
    $wp_customize->add_section('social_links', array(
        'title'    => 'Réseaux sociaux',
        'priority' => 30,
    ));
    
    // YouTube
    $wp_customize->add_setting('youtube_url', array(
        'default'   => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('youtube_url', array(
        'label'    => 'URL YouTube',
        'section'  => 'social_links',
        'type'     => 'url',
    ));
    
    // Instagram
    $wp_customize->add_setting('instagram_url', array(
        'default'   => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('instagram_url', array(
        'label'    => 'URL Instagram',
        'section'  => 'social_links',
        'type'     => 'url',
    ));
    
    // Mastodon
    $wp_customize->add_setting('mastodon_url', array(
        'default'   => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('mastodon_url', array(
        'label'    => 'URL Mastodon',
        'section'  => 'social_links',
        'type'     => 'url',
    ));
}
add_action('customize_register', 'theme_customize_register');