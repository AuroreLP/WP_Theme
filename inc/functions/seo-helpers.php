<?php
/**
 * SEO Helpers — Connexion du champ ACF "image_partage_social" à Rank Math
 *
 * @package turningpages
 */

add_action( 'rank_math/opengraph/facebook/add_images', 'tp_inject_og_image', 5 );
function tp_inject_og_image( $image_obj ) {
    if ( ! is_singular() || $image_obj->has_images() ) {
        return;
    }

    $post_id = get_queried_object_id();

    if ( ! $post_id ) {
        return;
    }

    // Lecture directe du post_meta (bypass ACF) pour fiabilité maximale.
    // ACF stocke les images sous la clé = nom du champ dans wp_postmeta.
    $raw = get_post_meta( $post_id, 'image_partage_social', true );

    // Log temporaire pour diagnostic — à retirer après confirmation.
    error_log( '[tp_og] post_id=' . $post_id . ' raw=' . print_r( $raw, true ) );

    if ( ! $raw ) {
        return;
    }

    // Le format de retour ACF peut être : ID entier, tableau avec 'ID', ou URL.
    if ( is_numeric( $raw ) && (int) $raw > 0 ) {
        $image_obj->add_image_by_id( (int) $raw );
    } elseif ( is_array( $raw ) && ! empty( $raw['ID'] ) ) {
        $image_obj->add_image_by_id( (int) $raw['ID'] );
    } elseif ( is_string( $raw ) && filter_var( $raw, FILTER_VALIDATE_URL ) ) {
        $image_obj->add_image_by_url( $raw );
    }
}
