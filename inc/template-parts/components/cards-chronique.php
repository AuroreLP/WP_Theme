<?php
/**
 * Template Part — Card: Chronique (Review)
 *
 * Renders a single chronique card for listing pages (page-chroniques.php,
 * front-page.php). Displays thumbnail, genre, title, date, and a hover
 * overlay with excerpt and link to the full review.
 *
 * Expected $args (passed via get_template_part):
 * - genre:  parent genre slug (for data-genre JS filtering)
 * - themes: space-separated theme slugs (for data-theme JS filtering)
 * - nation: nationality slug (for data-nation JS filtering)
 * - media:  media type slug (for data-media JS filtering)
 *
 * These data-* attributes are read by filter-chroniques.js to perform
 * client-side filtering and pagination.
 *
 * Genre display name is resolved here via tp_get_chronique_genre_display(),
 * which prefers the sub-genre name over the parent genre.
 *
 * @package turningpages
 */

$args = wp_parse_args( $args ?? array(), array(
    'genre'  => '',
    'themes' => '',
    'nation' => '',
    'media'  => '',
) );

// Get the most specific genre name for display (sub-genre > parent)
$genre_info = tp_get_chronique_genre_display();
$genre_name = $genre_info ? $genre_info['name'] : 'Non classé';
?>

<article
    class="post-box chroniques"
    data-category="chroniques"
    data-genre="<?php echo esc_attr( $args['genre'] ); ?>"
    data-theme="<?php echo esc_attr( $args['themes'] ); ?>"
    data-nation="<?php echo esc_attr( $args['nation'] ); ?>"
    data-media="<?php echo esc_attr( $args['media'] ); ?>"
>

    <?php /* Post thumbnail */ ?>
    <div class="article-img">
        <?php if ( has_post_thumbnail() ) : ?>
            <img
                src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>"
                alt="<?php echo esc_attr( get_the_title() ); ?>"
            >
        <?php endif; ?>
    </div>

    <?php /* Visible content: genre badge, title, date */ ?>
    <div class="article-presentation">
        <span class="category">
            <?php echo esc_html( $genre_name ); ?>
        </span>

        <h2 class="article-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <span class="article-date">
            <?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
        </span>
    </div>

    <?php /* Hover overlay: excerpt + arrow link */ ?>
    <div class="article-overlay">
        <div class="overlay-content">
            <p>
                <?php echo wp_trim_words( get_the_excerpt(), 45, '...' ); ?>
            </p>
            <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Lire la chronique : ' . get_the_title() ); ?>">
                Lire la chronique
                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                    <path class="arrow-path" fill="none" stroke-width="48"
                          stroke-linecap="square" stroke-miterlimit="10"
                          d="M268 112l144 144-144 144M392 256H100"/>
                </svg>
            </a>
        </div>
    </div>

</article>