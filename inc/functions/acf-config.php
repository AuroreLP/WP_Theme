<?php
/**
 * Configuration des champs ACF pour les bilans et chroniques
 */

// Créer automatiquement les mois de lecture (une seule fois)
function create_mois_lecture_terms() {
    // Vérifie si c'est déjà fait
    if (get_option('mois_lecture_created')) return;
    
    $annees = array(2024, 2025, 2026);
    $mois_noms = array(
        '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
        '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
        '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
        '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
    );
    
    foreach ($annees as $annee) {
        foreach ($mois_noms as $num => $nom) {
            wp_insert_term(
                "$nom $annee",
                'mois_lecture',
                array('slug' => "$annee-$num")
            );
        }
    }
    
    // Marquer comme fait
    update_option('mois_lecture_created', true);
}
add_action('init', 'create_mois_lecture_terms', 15);

// Créer les champs ACF pour les bilans
function create_acf_fields_for_bilans() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Récupérer dynamiquement l'ID de la catégorie Bilan
    $bilan_cat = get_category_by_slug('bilan');
    $bilan_id = $bilan_cat ? $bilan_cat->term_id : 54;
    
    acf_add_local_field_group(array(
        'key' => 'group_bilan_trimestriel',
        'title' => 'Configuration du Bilan Trimestriel',
        'fields' => array(
            array(
                'key' => 'field_mois_1',
                'label' => 'Mois 1 du trimestre',
                'name' => 'mois_1',
                'type' => 'select',
                'instructions' => 'Sélectionnez le premier mois',
                'required' => 1,
                'choices' => array(),
                'default_value' => '',
                'allow_null' => 0,
                'ui' => 1,
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_mois_2',
                'label' => 'Mois 2 du trimestre',
                'name' => 'mois_2',
                'type' => 'select',
                'instructions' => 'Sélectionnez le deuxième mois',
                'required' => 1,
                'choices' => array(),
                'default_value' => '',
                'allow_null' => 0,
                'ui' => 1,
                'return_format' => 'value',
            ),
            array(
                'key' => 'field_mois_3',
                'label' => 'Mois 3 du trimestre',
                'name' => 'mois_3',
                'type' => 'select',
                'instructions' => 'Sélectionnez le troisième mois',
                'required' => 1,
                'choices' => array(),
                'default_value' => '',
                'allow_null' => 0,
                'ui' => 1,
                'return_format' => 'value',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_category',
                    'operator' => '==',
                    'value' => $bilan_id,
                ),
            ),
        ),
    ));
}
add_action('acf/init', 'create_acf_fields_for_bilans');

// Ajouter le champ "Coup de cœur" aux chroniques
function create_acf_coup_de_coeur_field() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    acf_add_local_field_group(array(
        'key' => 'group_coup_de_coeur',
        'title' => 'Coup de cœur',
        'fields' => array(
            array(
                'key' => 'field_coup_de_coeur',
                'label' => '💚 Coup de cœur',
                'name' => 'coup_de_coeur',
                'type' => 'true_false',
                'instructions' => 'Cochez cette case si ce livre est un coup de cœur',
                'required' => 0,
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => 'Oui',
                'ui_off_text' => 'Non',
                'return_format' => 'boolean', // IMPORTANT : retourner un booléen
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'chroniques', // Pluriel comme votre CPT
                ),
            ),
        ),
        'menu_order' => 5,
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));
}
add_action('acf/init', 'create_acf_coup_de_coeur_field');

// Remplir dynamiquement les choix des mois
function load_mois_lecture_choices($field) {
    $field['choices'] = array();
    
    $terms = get_terms(array(
        'taxonomy' => 'mois_lecture',
        'hide_empty' => false,
        'orderby' => 'slug',
        'order' => 'DESC'
    ));
    
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $field['choices'][$term->slug] = $term->name;
        }
    } else {
        $field['choices'][''] = '⚠️ Aucun mois disponible';
    }
    
    return $field;
}

add_filter('acf/load_field/key=field_mois_1', 'load_mois_lecture_choices');
add_filter('acf/load_field/key=field_mois_2', 'load_mois_lecture_choices');
add_filter('acf/load_field/key=field_mois_3', 'load_mois_lecture_choices');

/**
 * Récupère les coups de cœur pour un trimestre donné
 * 
 * @param array $mois_trimestre Tableau des mois (format: '2024-01', '2024-02', etc.)
 * @return array Tableau des coups de cœur organisés par mois
 */
/**
 * Récupère les coups de cœur pour un trimestre donné
 * 
 * @param array $mois_trimestre Tableau des mois (format: '2024-01', '2024-02', etc.)
 * @return array Tableau des coups de cœur organisés par mois
 */
function get_coups_de_coeur_trimestre($mois_trimestre) {
    $coups_de_coeur = array();
    
    // Initialiser le tableau pour chaque mois
    foreach ($mois_trimestre as $mois) {
        $coups_de_coeur[$mois] = array();
    }
    
    // Pour chaque mois, récupérer les coups de cœur
    foreach ($mois_trimestre as $mois_slug) {
        $args = array(
            'post_type' => 'chroniques',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'coup_de_coeur',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'mois_lecture',
                    'field' => 'slug',
                    'terms' => $mois_slug,
                )
            ),
            'orderby' => 'date',
            'order' => 'ASC'
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $post_id = get_the_ID();
                
                // Récupérer les infos du livre
                $auteur_terms = wp_get_post_terms($post_id, 'auteur', array('fields' => 'names'));
                $auteur = !empty($auteur_terms) ? $auteur_terms[0] : 'Auteur inconnu';
                
                $note = get_post_meta($post_id, 'note_etoiles', true);
                
                // Ajouter le livre au bon mois
                $coups_de_coeur[$mois_slug][] = array(
                    'id' => $post_id,
                    'permalink' => get_permalink(),
                    'titre' => esc_html(get_the_title()),
                    'auteur' => esc_html($auteur),
                    'note' => $note ? floatval($note) : null,
                    'couverture' => get_the_post_thumbnail_url($post_id, 'medium')
                );
            }
            wp_reset_postdata();
        }
    }
    
    return $coups_de_coeur;
}
