<?php
/**
 * Template Part — Sidebar: Livre (Book)
 *
 * Displays metadata specific to book chroniques:
 * - Star rating (via shared rating template part)
 * - Publication year
 * - Page count
 *
 * Used in: sidebar.php (via get_template_part, when media type is 'livre')
 *
 * @package turningpages
 */
?>

<div class="book-info">

    <?php /* Star rating — shared component across all media sidebars */ ?>
    <?php get_template_part( 'inc/template-parts/chronique/rating' ); ?>

    <?php
    $date_pub = get_post_meta( get_the_ID(), 'date_publication', true );
    $pages    = get_post_meta( get_the_ID(), 'pages', true );
    ?>

    <?php if ( $date_pub ) : ?>
        <p>
            <strong>Année de publication :</strong>
            <?php echo esc_html( $date_pub ); ?>
        </p>
    <?php endif; ?>

    <?php if ( $pages ) : ?>
        <p>
            <strong>Pages :</strong>
            <?php echo esc_html( $pages ); ?>
        </p>
    <?php endif; ?>

</div>