<?php
/**
 * Search Configuration — Extended Taxonomy Search
 *
 * By default, WordPress search only looks at post_title and post_content.
 * This file extends the search query to also match against taxonomy terms
 * (categories, tags, genre, nationality, media type, etc.), so that
 * searching for "science-fiction" or "américain" returns matching posts.
 *
 * How it works:
 * 1. pre_get_posts hook intercepts the search query before it runs
 * 2. We add SQL JOINs to connect posts → term_relationships → terms
 * 3. We extend the WHERE clause to also match term names
 * 4. DISTINCT prevents duplicates when a post matches multiple terms
 *
 * This runs only on front-end search (not admin) and only on the main query.
 *
 * @package turningpages
 */

add_action( 'pre_get_posts', 'tp_extend_search_with_taxonomies' );
function tp_extend_search_with_taxonomies( $query ) {
    // Only modify the main front-end search query
    if ( ! $query->is_search() || is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $search_term = $query->get( 's' );

    // Skip empty or very short searches (single character = heavy query, few results)
    if ( ! $search_term || mb_strlen( trim( $search_term ) ) < 2 ) {
        return;
    }

    // Include all public content types in search results
    $query->set( 'post_type', array( 'post', 'chroniques', 'artiste' ) );
    $query->set( 'posts_per_page', 6 );
    $query->set( 'post_status', 'publish' );

    /**
     * JOIN the taxonomy tables so we can search term names.
     *
     * Chain: wp_posts → wp_term_relationships → wp_term_taxonomy → wp_terms
     * LEFT JOIN ensures posts without any terms still appear in results.
     */
    add_filter( 'posts_join', function ( $join ) {
        global $wpdb;
        $join .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id)";
        $join .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)";
        $join .= " LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)";
        return $join;
    } );

    /**
     * Extend the WHERE clause to match taxonomy term names.
     *
     * Uses $wpdb->prepare() for proper SQL escaping instead of manual
     * esc_sql(). The LIKE pattern matches the search term anywhere
     * within the term name.
     *
     * Only searches within specific taxonomies relevant to the site —
     * avoids matching internal/plugin taxonomies.
     */
    add_filter( 'posts_where', function ( $where ) use ( $search_term ) {
        global $wpdb;
        $like = '%' . $wpdb->esc_like( $search_term ) . '%';
        $where .= $wpdb->prepare(
            " OR ({$wpdb->posts}.post_status = 'publish' AND tt.taxonomy IN ('type_media','role','nationalite','genre','theme','category','post_tag') AND t.name LIKE %s)",
            $like
        );
        return $where;
    } );

    /**
     * Force DISTINCT to prevent duplicate posts.
     *
     * A post tagged with multiple matching terms would appear once per
     * match without DISTINCT. This single filter handles it — the
     * separate search_distinct() function from the previous version
     * was a duplicate and has been removed.
     */
    add_filter( 'posts_distinct', function () {
        return 'DISTINCT';
    } );
}