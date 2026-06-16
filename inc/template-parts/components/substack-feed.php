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

if ( ! function_exists( 'tp_normalize_title' ) ) {
    /**
     * Normalize a title for loose, accent/case/whitespace-insensitive comparison.
     *
     * @param string $title Title to normalize.
     * @return string
     */
    function tp_normalize_title( $title ) {
        $title = remove_accents( wp_strip_all_tags( $title ) );
        $title = strtolower( trim( $title ) );
        return preg_replace( '/\s+/', ' ', $title );
    }
}

if ( ! function_exists( 'tp_get_blog_post_titles' ) ) {
    /**
     * Normalized titles of all published blog posts (post + chroniques).
     *
     * Our own articles get re-published on Substack via rss.app, so they also
     * show up in Substack's RSS feed alongside genuinely original Substack
     * content. We use this list to filter those reposts out of the widget,
     * since they're already visible elsewhere on the site.
     *
     * Cached for an hour: this runs on every page that displays the widget.
     *
     * @return string[]
     */
    function tp_get_blog_post_titles() {
        $cached = get_transient( 'tp_blog_post_titles' );
        if ( false !== $cached ) {
            return $cached;
        }

        global $wpdb;
        $raw_titles = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT post_title FROM {$wpdb->posts} WHERE post_type IN (%s, %s) AND post_status = 'publish'",
                'post',
                'chroniques'
            )
        );

        $titles = array_map( 'tp_normalize_title', $raw_titles );

        set_transient( 'tp_blog_post_titles', $titles, HOUR_IN_SECONDS );

        return $titles;
    }
}

// Fetch more than we need: items matching a blog post title (reposted via
// rss.app) get filtered out below, so we need extra candidates to still end
// up with 3 genuinely original Substack items.
$candidates = $feed->get_items( 0, 15 );
if ( empty( $candidates ) ) {
    return;
}

$blog_titles = tp_get_blog_post_titles();

$items = array();
foreach ( $candidates as $candidate ) {
    if ( in_array( tp_normalize_title( $candidate->get_title() ), $blog_titles, true ) ) {
        continue; // Repost of one of our own articles — already shown elsewhere on the site.
    }
    $items[] = $candidate;
    if ( count( $items ) >= 3 ) {
        break;
    }
}

if ( empty( $items ) ) {
    return;
}
?>

<section class="substack-feed" aria-label="Dernières lettres Substack">

    <h2 class="substack-feed__heading">Ce que j'écris ailleurs</h2>
    <h3 class="substack-feed__subheading">Substack, un nouveau réseau social?</h3>
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

            // Method 1: media:thumbnail (RSS standard)
            $thumbnail = $item->get_thumbnail();
            $img_url   = ! empty( $thumbnail['url'] ) ? esc_url( $thumbnail['url'] ) : '';

            // Method 2: enclosure (alternative RSS format)
            if ( ! $img_url ) {
                $enclosure = $item->get_enclosure();
                if ( $enclosure && $enclosure->get_link() ) {
                    $img_url = esc_url( $enclosure->get_link() );
                }
            }

            // Method 3: first <img> found in post HTML content (Substack fallback)
            if ( ! $img_url ) {
                $content = $item->get_content();
                if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches ) ) {
                    $img_url = esc_url( $matches[1] );
                }
            }
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