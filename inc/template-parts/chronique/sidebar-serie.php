<?php
/**
 * Template Part — Sidebar: Série (TV Series)
 *
 * Displays metadata specific to series chroniques:
 * - Star rating (via shared rating template part)
 * - Release year
 * - Number of seasons
 * - Episode duration (formatted via tp_format_duree)
 *
 * Used in: sidebar.php (via get_template_part, when media type is 'serie')
 *
 * @package turningpages
 */
?>

<div class="book-info">

    <?php /* Star rating — shared component across all media sidebars */ ?>
    <?php get_template_part( 'inc/template-parts/chronique/rating' ); ?>

    <?php
    $date_sortie   = get_post_meta( get_the_ID(), 'date_sortie', true );
    $saisons       = get_post_meta( get_the_ID(), 'saisons', true );
    $duree_episode = (int) get_post_meta( get_the_ID(), 'duree_episode', true );
    ?>

    <?php if ( $date_sortie ) : ?>
        <p>
            <strong>Année de sortie :</strong>
            <?php echo esc_html( $date_sortie ); ?>
        </p>
    <?php endif; ?>

    <?php if ( $saisons ) : ?>
        <p>
            <strong>Saisons :</strong>
            <?php echo esc_html( $saisons ); ?>
        </p>
    <?php endif; ?>

    <?php if ( $duree_episode > 0 ) : ?>
        <p>
            <strong>Durée/épisode :</strong>
            <?php echo esc_html( tp_format_duree( $duree_episode ) ); ?>
        </p>
    <?php endif; ?>

</div>