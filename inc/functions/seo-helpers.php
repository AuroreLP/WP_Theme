<?php
/**
 * SEO Helpers — Connexion du champ ACF "image_partage_social" à Rank Math
 *
 * Le champ ACF "image_partage_social" (image paysage pour les réseaux sociaux)
 * existe pour les chroniques, articles et portraits, mais Rank Math free ne le
 * lit pas nativement. Ce hook l'injecte comme og:image avant que Rank Math ne
 * replie vers l'image à la une (portrait).
 *
 * @package turningpages
 */

add_action( 'rank_math/opengraph/facebook/add_images', 'tp_inject_og_image', 5 );
function tp_inject_og_image( $image_obj ) {
    if ( ! is_singular() || $image_obj->has_images() ) {
        return;
    }

    // get_the_ID() ne fonctionne pas hors boucle (contexte wp_head).
    // get_queried_object_id() est ce que Rank Math utilise lui-même.
    $post_id = get_queried_object_id();

    // get_field() gère tous les formats de retour ACF (ID, array, URL).
    $image = function_exists( 'get_field' ) ? get_field( 'image_partage_social', $post_id ) : null;

    if ( ! $image ) {
        return;
    }

    if ( is_array( $image ) && isset( $image['ID'] ) ) {
        $image_obj->add_image_by_id( (int) $image['ID'] );
    } elseif ( is_numeric( $image ) && (int) $image > 0 ) {
        $image_obj->add_image_by_id( (int) $image );
    } elseif ( is_string( $image ) && filter_var( $image, FILTER_VALIDATE_URL ) ) {
        $image_obj->add_image_by_url( $image );
    }
}
