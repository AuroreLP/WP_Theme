<?php
/**
 * Template Part — Sidebar: Film
 *
 * Displays metadata specific to film chroniques:
 * - Star rating (via shared rating template part)
 * - Release year
 * - Duration (formatted as Xh00 via tp_format_duree)
 *
 * Used in: sidebar.php (via get_template_part, when media type is 'film')
 *
 * @package turningpages
 */
?>

<div class="book-info">

    <?php /* Star rating — shared component across all media sidebars */ ?>
    <?php get_template_part( 'inc/template-parts/chronique/rating' ); ?>

    <?php
    $date_sortie = get_post_meta( get_the_ID(), 'date_sortie', true );
    $duree       = (int) get_post_meta( get_the_ID(), 'duree', true );
    ?>

    <?php if ( $date_sortie ) : ?>
        <p>
            <strong>Année de sortie :</strong>
            <?php echo esc_html( $date_sortie ); ?>
        </p>
    <?php endif; ?>

    <?php if ( $duree > 0 ) : ?>
        <p>
            <strong>Durée :</strong>
            <?php echo esc_html( tp_format_duree( $duree ) ); ?>
        </p>
    <?php endif; ?>

</div>
