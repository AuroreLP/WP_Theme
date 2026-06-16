<?php
/**
 * RSS Feed — Featured Image Injection
 *
 * WordPress's default RSS feed never includes the featured image: there's
 * no <enclosure>, no <media:content>, and the image isn't part of the
 * post content HTML unless the author placed it inline in the editor.
 *
 * This matters here because the combined articles + chroniques feed is
 * imported into Substack via rss.app, and both only pick up images that
 * are literally present as <img> tags in the feed item's content. Without
 * this filter, only posts where the image happens to also be inline in
 * the body show up with a picture on Substack — everything relying on
 * the "featured image" field is silently dropped.
 *
 * Fix: prepend the featured image (as a real <img> tag) to the feed
 * content for both 'post' and 'chroniques', so every item that has a
 * featured image carries it into the feed.
 *
 * @package turningpages
 */

add_filter( 'the_content_feed', 'tp_prepend_featured_image_to_feed' );
add_filter( 'the_excerpt_rss', 'tp_prepend_featured_image_to_feed' );
/**
 * Prepend the post's featured image to its RSS feed content/excerpt.
 *
 * @param string $content Feed item content or excerpt.
 * @return string
 */
function tp_prepend_featured_image_to_feed( $content ) {
    if ( ! in_array( get_post_type(), array( 'post', 'chroniques' ), true ) ) {
        return $content;
    }

    if ( ! has_post_thumbnail() ) {
        return $content;
    }

    $image = get_the_post_thumbnail(
        get_the_ID(),
        'large',
        array( 'style' => 'max-width: 100%; height: auto;' )
    );

    return $image . $content;
}
