<?php
/**
 * SEO Helpers — Connexion du champ ACF "image_partage_social" à Rank Math
 *
 * Rank Math free utilise l'image à la une comme og:image. Ce filtre
 * intercepte chaque URL d'image que Rank Math s'apprête à publier et la
 * remplace par le champ ACF "image_partage_social" si celui-ci est défini.
 *
 * Fonctionne pour tous les types de contenu (chroniques, articles, portraits).
 *
 * @package turningpages
 */

add_filter( 'rank_math/opengraph/facebook/image', 'tp_replace_og_image' );
function tp_replace_og_image( $url ) {
    if ( ! is_singular() ) {
        return $url;
    }

    $post_id = get_queried_object_id();
    if ( ! $post_id ) {
        return $url;
    }

    $raw = get_post_meta( $post_id, 'image_partage_social', true );
    if ( ! $raw ) {
        return $url;
    }

    // Format ID (entier ou chaîne numérique)
    if ( is_numeric( $raw ) && (int) $raw > 0 ) {
        $acf_url = wp_get_attachment_image_url( (int) $raw, 'full' );
        return $acf_url ?: $url;
    }

    // Format tableau (ACF stocke parfois l'image comme array sérialisé)
    if ( is_array( $raw ) ) {
        if ( ! empty( $raw['ID'] ) ) {
            $acf_url = wp_get_attachment_image_url( (int) $raw['ID'], 'full' );
            return $acf_url ?: $url;
        }
        if ( ! empty( $raw['url'] ) && filter_var( $raw['url'], FILTER_VALIDATE_URL ) ) {
            return $raw['url'];
        }
        return $url;
    }

    // Format URL directe
    if ( is_string( $raw ) && filter_var( $raw, FILTER_VALIDATE_URL ) ) {
        return $raw;
    }

    return $url;
}
