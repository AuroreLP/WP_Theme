<?php
/**
 * Configuration et filtres de recherche
 */

// Inclure les custom post types dans la recherche
function extend_search_with_taxonomies($query) {
    if ($query->is_search() && !is_admin() && $query->is_main_query()) {
        global $wpdb;

        $search_term = $query->get('s');
        if (!$search_term) return;

        $post_types = array('post', 'chroniques', 'artiste');
        $query->set('post_type', $post_types);
        $query->set('posts_per_page', 6);
        $query->set('post_status', 'publish');

        add_filter('posts_join', function($join) use ($wpdb) {
            $join .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id)
                       LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                       LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id) ";
            return $join;
        });

        add_filter('posts_where', function($where) use ($wpdb, $search_term) {
            $search_term_esc = esc_sql($wpdb->esc_like($search_term));
            $where .= " OR (tt.taxonomy IN ('type_media','role','nationalite','genre','theme','category','post_tag') 
                             AND t.name LIKE '%{$search_term_esc}%') ";
            return $where;
        });

        add_filter('posts_distinct', function() { return 'DISTINCT'; });
    }
}
add_action('pre_get_posts', 'extend_search_with_taxonomies');



// Forcer DISTINCT pour éviter les doublons
function search_distinct($distinct) {
    if (is_search()) {
        return 'DISTINCT';
    }
    return $distinct;
}
add_filter('posts_distinct', 'search_distinct');