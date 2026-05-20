<?php
/**
 * SEO Helpers — Rank Math OpenGraph Fixes
 *
 * Rank Math does not correctly apply the custom per-post Facebook image
 * (rank_math_facebook_image_id) for the standard 'post' type: it falls
 * through to the featured image instead. This file fixes that by reading
 * the meta directly and injecting it before Rank Math's own detection runs.
 *
 * @package turningpages
 */

/**
 * Inject the custom Rank Math og:image for singular 'post' pages.
 *
 * The 'rank_math/opengraph/facebook/add_images' action fires before
 * Rank Math's built-in image selection (featured image, content images).
 * Adding an image here means Rank Math's set_singular_image() will see
 * has_images() === true and skip the featured-image fallback.
 *
 * Priority 5 ensures we run before any other add_images hooks.
 */
add_action( 'rank_math/opengraph/facebook/add_images', 'tp_fix_post_og_image', 5 );
function tp_fix_post_og_image( $image_obj ) {
    if ( ! is_singular( 'post' ) || $image_obj->has_images() ) {
        return;
    }

    $post_id  = get_the_ID();
    $image_id = (int) get_post_meta( $post_id, 'rank_math_facebook_image_id', true );

    if ( $image_id > 0 ) {
        $image_obj->add_image_by_id( $image_id );
    }
}
