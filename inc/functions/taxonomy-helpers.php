<?php
/**
 * Taxonomy Display Helpers
 *
 * Utility functions for rendering taxonomy terms in templates.
 * Used primarily in chronique sidebars, article footers, and card components
 * to display genres, themes, authors, nationalities, etc.
 *
 * Naming convention:
 * - tp_get_*   → returns data (for logic, filters, JS)
 * - tp_display_* → echoes HTML (for direct use in templates)
 *
 * @package turningpages
 */


/* =========================================================================
 * GENERIC TERM LIST RENDERER
 * ========================================================================= */

/**
 * Display terms of any taxonomy as an HTML <li> list with archive links.
 *
 * This is the core function that powers all the specific display functions
 * below. Instead of repeating the same get_the_terms/foreach/echo pattern
 * for every taxonomy, they all call this.
 *
 * @param string   $taxonomy  The taxonomy slug (e.g. 'genre', 'theme').
 * @param int|null $post_id   Post ID. Defaults to current post in the loop.
 */
function tp_display_terms_list( $taxonomy, $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $terms = get_the_terms( $post_id, $taxonomy );
    if ( ! $terms || is_wp_error( $terms ) ) {
        return;
    }

    foreach ( $terms as $term ) {
        echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
    }
}


/* =========================================================================
 * GENRE — Special handling (sub-genre priority)
 * ========================================================================= */

/**
 * Get the most specific genre to display.
 *
 * Genre is a hierarchical taxonomy (parent/child). When both a parent
 * genre (e.g. "Fiction") and a sub-genre (e.g. "Science-fiction") are
 * assigned, we prefer the sub-genre for display. Falls back to the
 * parent if no sub-genre exists.
 *
 * @return array|null  Array with 'name', 'slug', 'link', 'term' keys, or null.
 */
function tp_get_chronique_genre_display() {
    $genres = get_the_terms( get_the_ID(), 'genre' );

    if ( ! $genres || is_wp_error( $genres ) ) {
        return null;
    }

    $genre_parent = null;
    $sous_genre   = null;

    foreach ( $genres as $genre ) {
        if ( $genre->parent == 0 ) {
            $genre_parent = $genre;
        } else {
            $sous_genre = $genre;
            break; // Take the first sub-genre found
        }
    }

    $display = $sous_genre ? $sous_genre : $genre_parent;

    if ( $display ) {
        return array(
            'name' => $display->name,
            'slug' => $display->slug,
            'link' => get_term_link( $display ),
            'term' => $display,
        );
    }

    return null;
}

/**
 * Display genres as <li> list — sub-genres preferred, parent as fallback.
 *
 * Unlike the generic tp_display_terms_list(), this function applies the
 * sub-genre priority logic: if sub-genres exist, only they are shown.
 * If none, the parent genre is displayed instead.
 */
function tp_display_chronique_genres_list() {
    $genres = get_the_terms( get_the_ID(), 'genre' );
    if ( ! $genres || is_wp_error( $genres ) ) {
        return;
    }

    $genre_parent = null;
    $sous_genres  = array();

    foreach ( $genres as $genre ) {
        if ( $genre->parent == 0 ) {
            $genre_parent = $genre;
        } else {
            $sous_genres[] = $genre;
        }
    }

    if ( ! empty( $sous_genres ) ) {
        foreach ( $sous_genres as $sg ) {
            echo '<li><a href="' . esc_url( get_term_link( $sg ) ) . '">' . esc_html( $sg->name ) . '</a></li>';
        }
    } elseif ( $genre_parent ) {
        echo '<li><a href="' . esc_url( get_term_link( $genre_parent ) ) . '">' . esc_html( $genre_parent->name ) . '</a></li>';
    }
}


/* =========================================================================
 * SPECIFIC TAXONOMY DISPLAY FUNCTIONS
 *
 * These are thin wrappers around tp_display_terms_list().
 * They exist for readability in templates — calling
 * tp_display_chronique_themes_list() is clearer than
 * tp_display_terms_list('theme') when reading a sidebar file.
 * ========================================================================= */

/** Display themes as <li> list with archive links. */
function tp_display_chronique_themes_list() {
    tp_display_terms_list( 'theme' );
}

/** Display authors as <li> list with archive links. */
function tp_display_chronique_auteurs_list() {
    tp_display_terms_list( 'auteur' );
}

/** Display nationalities as <li> list with archive links. */
function tp_display_chronique_nationalites_list() {
    tp_display_terms_list( 'nationalite' );
}

/** Display roles as <li> list with archive links. */
function tp_display_chronique_roles_list() {
    tp_display_terms_list( 'role' );
}


/* =========================================================================
 * TAG HELPERS
 * ========================================================================= */

/**
 * Display post tags as <li> list with archive links.
 *
 * Uses get_the_tags() (WP core) instead of get_the_terms() because
 * tags use the built-in 'post_tag' taxonomy with its own helper.
 */
function tp_display_chronique_tags_list() {
    $tags = get_the_tags();
    if ( ! $tags || is_wp_error( $tags ) ) {
        return;
    }

    foreach ( $tags as $tag ) {
        echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></li>';
    }
}

/**
 * Display tags as comma-separated inline links.
 * Used in article footers where a list layout isn't appropriate.
 */
function tp_display_chronique_tags_inline() {
    $tags = get_the_tags();
    if ( ! $tags || is_wp_error( $tags ) ) {
        return;
    }

    $tag_links = array();
    foreach ( $tags as $tag ) {
        $tag_links[] = '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
    }
    echo implode( ', ', $tag_links );
}


/* =========================================================================
 * DATA RETRIEVAL (for JS filters, card components, etc.)
 * ========================================================================= */

/**
 * Get themes for the current post.
 *
 * @return WP_Term[]|null  Array of term objects, or null if none.
 */
function tp_get_chronique_themes() {
    $themes = get_the_terms( get_the_ID(), 'theme' );
    if ( ! $themes || is_wp_error( $themes ) ) {
        return null;
    }
    return $themes;
}

/**
 * Get tags for the current post.
 *
 * @return WP_Term[]|null  Array of term objects, or null if none.
 */
function tp_get_chronique_tags() {
    $tags = get_the_tags();
    if ( ! $tags || is_wp_error( $tags ) ) {
        return null;
    }
    return $tags;
}


/* =========================================================================
 * RANK MATH — Custom SEO Variable
 * ========================================================================= */

/**
 * Register a custom Rank Math variable: %chronique_auteur%
 *
 * This allows using %chronique_auteur% in Rank Math title/description
 * templates for chroniques. It pulls the author name from the 'auteur'
 * taxonomy and formats it as "— Author Name".
 *
 * Example Rank Math title pattern:
 *   %title% %chronique_auteur% | %sitename%
 *   → "Misery — Stephen King | L'Ivresse des Mots"
 *
 * Only registers if Rank Math is active (function_exists check).
 */
add_action( 'init', 'tp_register_rankmath_author_variable' );
function tp_register_rankmath_author_variable() {
    if ( ! function_exists( 'rank_math_register_var_replacement' ) ) {
        return;
    }

    rank_math_register_var_replacement(
        'chronique_auteur',
        array(
            'name'        => 'Auteur de la chronique',
            'description' => "Nom de l'auteur depuis la taxonomy auteur",
            'variable'    => 'chronique_auteur',
            'example'     => "Nom de l'auteur",
        ),
        function () {
            $post_id = get_the_ID();
            if ( ! $post_id ) {
                return '';
            }

            $auteur_terms = get_the_terms( $post_id, 'auteur' );
            if ( ! empty( $auteur_terms ) && ! is_wp_error( $auteur_terms ) ) {
                return '— ' . implode( ', ', wp_list_pluck( $auteur_terms, 'name' ) );
            }

            return '';
        }
    );
}