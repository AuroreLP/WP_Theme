<?php
/**
 * Template Part — Card: Article (Blog Post)
 *
 * Renders a single article card for listing pages (page-articles.php,
 * front-page.php). Displays thumbnail, category, title, date, and
 * a hover overlay with excerpt and read link.
 *
 * Expected $args (passed via get_template_part):
 * - category_slug: used for data-attribute (JS filtering)
 * - category_name: displayed as the category label
 *
 * @package turningpages
 */

$args = wp_parse_args( $args ?? array(), array(
    'category_slug' => '',
    'category_name' => '',
) );
?>

<article class="post-box articles" data-category="<?php echo esc_attr( $args['category_slug'] ); ?>">

    <?php /* Post thumbnail */ ?>
    <div class="article-img">
        <?php if ( has_post_thumbnail() ) : ?>
            <img
                src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>"
                alt="<?php echo esc_attr( get_the_title() ); ?>"
            >
        <?php endif; ?>
    </div>

    <?php /* Visible content: category badge, title, date */ ?>
    <div class="article-presentation">
        <?php if ( $args['category_name'] ) : ?>
            <span class="category">
                <?php echo esc_html( $args['category_name'] ); ?>
            </span>
        <?php endif; ?>

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
                <?php echo wp_trim_words( get_the_excerpt(), 50, '...' ); ?>
            </p>
            <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Lire l\'article : ' . get_the_title() ); ?>">
                Lire l'article
                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                    <path class="arrow-path" fill="none" stroke-width="48"
                          stroke-linecap="square" stroke-miterlimit="10"
                          d="M268 112l144 144-144 144M392 256H100"/>
                </svg>
            </a>
        </div>
    </div>

</article>