<?php
/**
 * Template Part — Social Links
 *
 * Reusable block of social media icons pulled from the WordPress Customizer.
 * Used in both header.php and footer.php to keep a single source of truth.
 *
 * Usage:
 *   get_template_part( 'inc/template-parts/components/social-links' );
 *
 * To add a new social network:
 *   1. Add a theme_mod field in the Customizer (e.g. 'tiktok_url')
 *   2. Add an entry to the $social_links array below
 *
 * @package turningpages
 */

$social_links = array(
    array(
        'mod'   => 'youtube_url',
        'icon'  => 'logo-youtube',
        'label' => 'YouTube',
    ),
    array(
        'mod'   => 'instagram_url',
        'icon'  => 'logo-instagram',
        'label' => 'Instagram',
    ),
    array(
        'mod'   => 'mastodon_url',
        'icon'  => 'logo-mastodon',
        'label' => 'Mastodon',
    ),
);

foreach ( $social_links as $link ) :
    $url = get_theme_mod( $link['mod'] );
    if ( $url ) : ?>
        <a href="<?php echo esc_url( $url ); ?>"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="<?php echo esc_attr( $link['label'] ); ?>">
            <ion-icon name="<?php echo esc_attr( $link['icon'] ); ?>" aria-hidden="true"></ion-icon>
        </a>
    <?php endif;
endforeach;