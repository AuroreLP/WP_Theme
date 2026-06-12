<?php
/**
 * Template Part — Substack Feed
 *
 * Displays the 3 latest Substack posts via the native WordPress RSS reader.
 * Uses fetch_feed() (SimplePie) with built-in WordPress transient caching.
 *
 * Usage: get_template_part( 'inc/template-parts/components/substack-feed' );
 *
 * @package turningpages
 */

$substack_url = get_theme_mod( 'substack_url' );
if ( ! $substack_url ) {
    return;
}

$rss_url = trailingslashit( esc_url_raw( $substack_url ) ) . 'feed';

include_once ABSPATH . WPINC . '/feed.php';
$feed = fetch_feed( $rss_url );

if ( is_wp_error( $feed ) ) {
    return;
}

$items = $feed->get_items( 0, 3 );
if ( empty( $items ) ) {
    return;
}
?>

<section class="substack-feed" aria-label="Dernières lettres Substack">

    <h2 class="substack-feed__heading">Ce que j'écris ailleurs</h2>

    <p class="substack-feed__intro">
        Substack, c'est un peu le réseau social des gens qui aiment lire et écrire, 
        sans algorithme qui dicte quoi publier ni comment. Loin des injonctions 
        d'Instagram, j'y explore une facette plus personnelle, sans artifice : 
        des réflexions en cours, des pensées plus personnelles, des textes 
        qui ne rentrent pas dans le cadre du blog.
    </p>

    <ul class="substack-feed__list">
        <?php foreach ( $items as $item ) :
            $title       = esc_html( $item->get_title() );
            $link        = esc_url( $item->get_permalink() );
            $date        = $item->get_date( 'd/m/Y' );
            $description = wp_trim_words( wp_strip_all_tags( $item->get_description() ), 20, '…' );
            $thumbnail   = $item->get_thumbnail();
            $img_url     = ! empty( $thumbnail['url'] ) ? esc_url( $thumbnail['url'] ) : '';
        ?>
        <li class="substack-feed__item">
            <a href="<?php echo $link; ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="substack-feed__link">

                <?php if ( $img_url ) : ?>
                <div class="substack-feed__img-wrap">
                    <img src="<?php echo $img_url; ?>"
                         alt="<?php echo $title; ?>"
                         class="substack-feed__img"
                         loading="lazy"
                         decoding="async">
                </div>
                <?php endif; ?>

                <div class="substack-feed__meta">
                    <p class="substack-feed__date"><?php echo esc_html( $date ); ?></p>
                    <h3 class="substack-feed__title"><?php echo $title; ?></h3>
                    <p class="substack-feed__desc"><?php echo esc_html( $description ); ?></p>
                </div>

            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="substack-feed__cta">
        <a href="<?php echo esc_url( $substack_url ); ?>"
           target="_blank"
           rel="noopener noreferrer"
           class="substack-feed__cta-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M22.539 8.242H1.46V5.406h21.08v2.836zM1.46 10.812V24L12 18.11 22.54 24V10.812H1.46zM22.54 0H1.46v2.836h21.08V0z"/>
            </svg>
            Rejoins-moi sur Substack
        </a>
    </div>

</section>