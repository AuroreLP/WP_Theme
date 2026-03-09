<?php
/**
 * Template Part — Sidebar Router
 *
 * Determines the current chronique's media type and loads the
 * corresponding sidebar template part. Acts as a dispatcher:
 *
 *   type_media slug → sidebar file loaded
 *   ─────────────────────────────────────
 *   livre          → sidebar-livre.php
 *   film           → sidebar-film.php
 *   serie          → sidebar-serie.php
 *   podcast        → sidebar-podcast.php
 *   (anything else)→ sidebar-default.php
 *
 * This keeps single-chroniques.php clean — it just calls:
 *   get_template_part('inc/template-parts/chronique/sidebar');
 * and this file handles the rest.
 *
 * Used in: single-chroniques.php
 *
 * @package turningpages
 */

$type = get_the_terms( get_the_ID(), 'type_media' );

$type_slug = ( $type && ! is_wp_error( $type ) )
    ? $type[0]->slug
    : 'default';

get_template_part( 'inc/template-parts/chronique/sidebar', $type_slug );