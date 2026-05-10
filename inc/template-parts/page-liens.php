<?php
/**
 * Template Part — Page Liens
 *
 * The visual content of the standalone links page:
 * logo + tagline, mini bio, "Découvrir le blog" CTA,
 * last 4 articles, social network buttons, newsletter CTA.
 *
 * To customise:
 * - Mini bio    → edit the page content in WordPress admin
 * - Social URLs → Appearance > Customize > Réseaux sociaux
 *
 * @package turningpages
 */

// Logo — same resolution logic as header.php
$default_logo = get_template_directory_uri() . '/assets/images/logos/light_logo.png';
$logo_url     = function_exists( 'tp_get_logo_url' ) ? tp_get_logo_url( 'light', 'full' ) : '';
$logo_src     = $logo_url ? $logo_url : $default_logo;

// Page content → used as "À propos" mini bio
$about_text = '';
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        $about_text = get_the_content();
    }
}

// Last 4 published articles (posts + chroniques), newest first
$liens_query = new WP_Query( array(
    'post_type'      => array( 'post', 'chroniques' ),
    'posts_per_page' => 4,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
) );

// Social URLs from Customizer (Appearance > Customize > Réseaux sociaux)
$instagram_url  = get_theme_mod( 'instagram_url' );
$mastodon_url   = get_theme_mod( 'mastodon_url' );
$bluesky_url    = get_theme_mod( 'bluesky_url' );
$threads_url    = get_theme_mod( 'threads_url' );
$youtube_url    = get_theme_mod( 'youtube_url' );
$newsletter_url = get_theme_mod( 'newsletter_url' );
?>

<div class="liens-wrapper">

    <header class="liens-header">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="liens-logo-link" aria-label="Accueil">
            <img
                id="site-logo"
                class="liens-logo"
                src="<?php echo esc_url( $logo_src ); ?>"
                alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                width="120"
                height="40"
            >
        </a>
        <h1 class="liens-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
        <?php $tagline = get_bloginfo( 'description' ); if ( $tagline ) : ?>
            <p class="liens-tagline"><?php echo esc_html( $tagline ); ?></p>
        <?php endif; ?>
    </header>

    <?php if ( $about_text ) : ?>
    <div class="liens-about-card">
        <span class="liens-label">À PROPOS</span>
        <div class="liens-about-text">
            <?php echo wp_kses_post( $about_text ); ?>
        </div>
    </div>
    <?php endif; ?>

    <a class="liens-cta-discover" href="<?php echo esc_url( home_url( '/' ) ); ?>">
        Découvrir le blog →
    </a>

    <?php if ( $liens_query->have_posts() ) : ?>
    <section class="liens-articles">
        <h2 class="liens-label">DERNIERS ARTICLES</h2>

        <?php while ( $liens_query->have_posts() ) : $liens_query->the_post(); ?>
            <?php
            if ( get_post_type() === 'chroniques' ) {
                $type_label = 'Chronique';
                $genre_info = tp_get_chronique_genre_display();
                $cat_label  = $genre_info ? $genre_info['name'] : '';
            } else {
                $type_label = 'Article';
                $categories = get_the_category();
                $cat_label  = ! empty( $categories ) ? $categories[0]->name : '';
            }
            $meta = $cat_label ? $type_label . ' · ' . $cat_label : $type_label;
            ?>
            <a class="liens-article-card" href="<?php the_permalink(); ?>">
                <span class="liens-article-meta"><?php echo esc_html( $meta ); ?></span>
                <span class="liens-article-title"><?php the_title(); ?></span>
            </a>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </section>
    <?php endif; ?>

    <?php if ( $instagram_url || $mastodon_url || $bluesky_url || $threads_url || $youtube_url ) : ?>
    <section class="liens-social">
        <h2 class="liens-label">ME RETROUVER</h2>
        <div class="liens-social-grid">

            <?php if ( $instagram_url ) : ?>
            <a href="<?php echo esc_url( $instagram_url ); ?>"
               class="liens-social-btn"
               target="_blank" rel="noopener noreferrer"
               aria-label="Instagram">
                <ion-icon name="logo-instagram" aria-hidden="true"></ion-icon>
                Instagram
            </a>
            <?php endif; ?>

            <?php if ( $mastodon_url ) : ?>
            <a href="<?php echo esc_url( $mastodon_url ); ?>"
               class="liens-social-btn"
               target="_blank" rel="noopener noreferrer"
               aria-label="Mastodon">
                <ion-icon name="logo-mastodon" aria-hidden="true"></ion-icon>
                Mastodon
            </a>
            <?php endif; ?>

            <?php if ( $bluesky_url ) : ?>
            <a href="<?php echo esc_url( $bluesky_url ); ?>"
               class="liens-social-btn"
               target="_blank" rel="noopener noreferrer"
               aria-label="Bluesky">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 568 501" aria-hidden="true" focusable="false">
                    <path d="M123.121 33.664C188.241 82.553 258.281 181.68 284 234.873c25.719-53.192 95.759-152.32 160.879-201.209C491.866-1.611 568-28.906 568 57.947c0 17.346-9.945 145.713-15.778 166.555-20.275 72.453-94.155 90.933-159.875 79.748C507.222 323.8 536.444 406.458 521.995 489.88c-24.846 137.737-219.986 73.084-218.03-36.641-.296-14.554-.59-29.115-1.037-43.67-1.035 14.515-2.071 29.076-2.94 43.67C298.04 562.964 102.9 626.617 78.055 489.88c-14.449-83.422 13.773-166.08 128.648-185.63-65.72 11.185-139.6-7.295-159.875-79.748C40.995 203.66 31.05 75.293 31.05 57.947 31.05-28.906 107.184-1.611 123.121 33.664Z"/>
                </svg>
                Bluesky
            </a>
            <?php endif; ?>

            <?php if ( $threads_url ) : ?>
            <a href="<?php echo esc_url( $threads_url ); ?>"
               class="liens-social-btn"
               target="_blank" rel="noopener noreferrer"
               aria-label="Threads">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 192" aria-hidden="true" focusable="false">
                    <path d="M141.537 88.988a66.667 66.667 0 0 0-2.518-1.143c-1.482-27.307-16.403-42.94-41.457-43.1h-.34c-14.986 0-27.449 6.396-35.12 18.036l13.779 9.452c5.73-8.695 14.724-10.548 21.348-10.548h.229c8.249.053 14.474 2.452 18.503 7.129 2.932 3.405 4.893 8.111 5.864 14.05-7.314-1.243-15.224-1.626-23.68-1.14-23.82 1.371-39.134 15.264-38.105 34.568.522 9.792 5.4 18.216 13.735 23.719 7.047 4.652 16.124 6.927 25.557 6.412 12.458-.683 22.231-5.436 29.049-14.127 5.178-6.6 8.453-15.153 9.899-25.93 5.937 3.583 10.337 8.298 12.767 13.966 4.132 9.635 4.373 25.468-8.546 38.376C124.866 160.16 110.986 166 92.099 166c-21.4-.017-37.742-6.959-48.558-20.625C33.979 133.026 28.5 114.465 28.25 91.5c.25-22.965 5.729-41.526 15.291-53.875C54.357 23.959 70.699 17.017 92.099 17c21.472.017 37.936 6.977 48.984 20.67 5.48 6.783 9.606 15.318 12.302 25.303l16.259-4.094c-3.285-12.107-8.522-22.607-15.649-31.208C139.752 10.812 118.185 1.019 92.099 1 65.933 1.018 44.529 10.832 29.943 28.24 16.816 44.882 10.004 67.842 9.75 91.5v.062c.254 23.658 7.066 46.618 20.193 63.26C44.529 173.23 65.933 183.044 92.1 183.062c21.547 0 39.747-6.918 53.132-20.28 16.864-16.836 17.687-38.958 11.705-52.313-4.395-10.245-13.777-18.498-27.4-23.481Zm-47.726 44.197c-10.443.578-21.287-4.104-21.82-14.173-.4-7.521 5.34-15.919 22.632-16.916a98.386 98.386 0 0 1 5.659-.162c6.02 0 11.843.49 17.374 1.448-1.977 24.649-13.58 29.274-23.845 29.803Z"/>
                </svg>
                Threads
            </a>
            <?php endif; ?>

            <?php if ( $youtube_url ) : ?>
            <a href="<?php echo esc_url( $youtube_url ); ?>"
               class="liens-social-btn"
               target="_blank" rel="noopener noreferrer"
               aria-label="YouTube">
                <ion-icon name="logo-youtube" aria-hidden="true"></ion-icon>
                YouTube
            </a>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

    <?php if ( $newsletter_url ) : ?>
    <a class="liens-newsletter-btn"
       href="<?php echo esc_url( $newsletter_url ); ?>"
       target="_blank" rel="noopener noreferrer">
        <ion-icon name="mail-outline" aria-hidden="true"></ion-icon>
        S'abonner à la newsletter
    </a>
    <?php endif; ?>

    <p class="liens-footer-link">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
        </a>
    </p>

</div>
