<?php
/**
 * Template Part — Card: Artiste (Portrait)
 *
 * Renders a single artiste card for the listing page (page-artistes.php).
 * Displays thumbnail, role, name, nationality, and a hover overlay
 * with excerpt and link to the portrait page.
 *
 * Expected $args (passed via get_template_part):
 * - role:   the artiste's role name (not slug — preserves point médian
 *           characters like "Auteur·ice" for data-role JS matching)
 * - nation: nationality slug (for data-nation JS filtering)
 *
 * @package turningpages
 */

$args = wp_parse_args( $args ?? array(), array(
    'role'   => '',
    'nation' => '',
) );
?>

<article class="post-box artiste"
         data-role="<?php echo esc_attr( $args['role'] ); ?>"
         data-nation="<?php echo esc_attr( $args['nation'] ); ?>">

    <?php /* Post thumbnail */ ?>
    <div class="article-img">
        <?php if ( has_post_thumbnail() ) : ?>
            <img
                src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>"
                alt="<?php echo esc_attr( get_the_title() ); ?>"
            >
        <?php endif; ?>
    </div>

    <?php /* Visible content: role badge, name, nationality */ ?>
    <div class="article-presentation">
        <?php if ( $args['role'] ) : ?>
            <span class="category"><?php echo wp_kses_post( $args['role'] ); ?></span>
        <?php endif; ?>

        <h2 class="article-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <?php if ( $args['nation'] ) : ?>
            <span class="article-nation"><?php echo esc_html( $args['nation'] ); ?></span>
        <?php endif; ?>
    </div>

    <?php /* Hover overlay: excerpt + portrait link */ ?>
    <div class="article-overlay">
        <div class="overlay-content">
            <p><?php echo wp_trim_words( get_the_excerpt(), 50, '...' ); ?></p>
            <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Lire le portrait : ' . get_the_title() ); ?>">
                Lire le portrait
            </a>
        </div>
    </div>

</article>