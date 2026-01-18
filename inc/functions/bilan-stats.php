<?php
/**
 * Fonctions pour les statistiques des bilans trimestriels
 */

/**
 * Calcule les statistiques d'un trimestre en récupérant les chroniques
 * 
 * @param array $mois Exemple: ['2024-10', '2024-11', '2024-12'] pour Q4 2024
 * @return array Tableau avec toutes les stats
 */
function get_trimestre_stats($mois) {
    // Validation : s'assurer que $mois est un tableau
    if (!is_array($mois)) {
        return array();
    }

    $stats = array(
        'total' => array(
            'livres' => 0,
            'pages' => 0,
            'heures' => 0,
        ),
        'par_mois' => array(),
        'nationalites' => array(),
        'genres' => array(),
        'auteurs_femmes' => 0,
        'auteurs_hommes' => 0,
        'auteurs_total' => 0,
        'coups_de_coeur' => array(),
    );
    
    // Pour chaque mois du trimestre
    foreach ($mois as $mois_slug) {
        // Sanitize le slug pour éviter les injections SQL
        $mois_slug = sanitize_text_field($mois_slug);

        $args = array(
            'post_type' => 'chroniques',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'mois_lecture',
                    'field' => 'slug',
                    'terms' => $mois_slug,
                ),
            ),
        );
        
        $query = new WP_Query($args);
        
        $stats_mois = array(
            'livres' => 0,
            'pages' => 0,
            'heures' => 0,
        );
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                
                // Comptage livres
                $stats['total']['livres']++;
                $stats_mois['livres']++;
                
                // Comptage pages
                $pages = get_post_meta($post_id, 'pages', true);
                if ($pages && is_numeric($pages)) {
                    $stats['total']['pages'] += absint($pages);
                    $stats_mois['pages'] += absint($pages);
                }
                
                // Comptage heures
                $heures = get_post_meta($post_id, 'heures_ecoute', true);
                if ($heures && is_numeric($heures)) {
                    $heures_float = floatval($heures);
                    $stats['total']['heures'] += $heures_float;
                    $stats_mois['heures'] += $heures_float;
                }
                
                // Nationalités
                $nationalites = wp_get_post_terms($post_id, 'nationalite', array('fields' => 'names'));
                if (!is_wp_error($nationalites)) {
                    foreach ($nationalites as $nat) {
                        $nat = sanitize_text_field($nat);
                        if (!isset($stats['nationalites'][$nat])) {
                            $stats['nationalites'][$nat] = 0;
                        }
                        $stats['nationalites'][$nat]++;
                    }
                }
                
                // Genres
                $genres = wp_get_post_terms($post_id, 'genre', array('fields' => 'names'));
                if (!is_wp_error($genres)) {
                    foreach ($genres as $genre) {
                        $genre = sanitize_text_field($genre);
                        if (!isset($stats['genres'][$genre])) {
                            $stats['genres'][$genre] = 0;
                        }
                        $stats['genres'][$genre]++;
                    }
                }
                
                // Sexe auteur
                $sexe_auteur = get_post_meta($post_id, 'sexe_auteur', true);
                $sexe_auteur = sanitize_text_field($sexe_auteur);
                if ($sexe_auteur === 'femme') {
                    $stats['auteurs_femmes']++;
                    $stats['auteurs_total']++;
                } elseif ($sexe_auteur === 'homme') {
                    $stats['auteurs_hommes']++;
                    $stats['auteurs_total']++;
                }
            }
        }
        
        wp_reset_postdata();
        $stats['par_mois'][$mois_slug] = $stats_mois;
    }
    $stats['coups_de_coeur'] = get_coups_de_coeur_trimestre($mois);

    return $stats;
}
