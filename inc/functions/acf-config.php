<?php
/**
 * ACF Field Configuration — Bilans & Chroniques
 *
 * Registers ACF field groups programmatically (via acf_add_local_field_group)
 * instead of through the ACF admin UI. This keeps field definitions in code,
 * version-controlled and portable across environments.
 *
 * Contents:
 * 1. Auto-creation of "mois de lecture" taxonomy terms (months for reading log)
 * 2. ACF field group for quarterly bilan posts (3 month selectors)
 * 3. ACF "Coup de coeur" toggle for chroniques
 * 4. Dynamic population of month select fields
 * 5. Helper to retrieve coups de coeur for a given quarter
 *
 * @package turningpages
 */


/* =========================================================================
 * 1. AUTO-CREATE READING MONTH TERMS
 * ========================================================================= */

/**
 * Populate the 'mois_lecture' taxonomy with month terms.
 *
 * Creates terms like "Janvier 2024" (slug: 2024-01) for each month/year
 * combination. Runs once then sets an option flag to avoid re-running.
 *
 * NOTE: Years are hardcoded. When 2027 approaches, add it to the array
 * or refactor to generate dynamically based on current year.
 *
 * Hooked at priority 15 (after taxonomy registration at default 10).
 */
add_action( 'init', 'tp_create_mois_lecture_terms', 15 );
function tp_create_mois_lecture_terms() {
    if ( get_option( 'mois_lecture_created' ) ) {
        return;
    }

    $annees    = array( 2024, 2025, 2026 );
    $mois_noms = array(
        '01' => 'Janvier',   '02' => 'Février',  '03' => 'Mars',
        '04' => 'Avril',     '05' => 'Mai',      '06' => 'Juin',
        '07' => 'Juillet',   '08' => 'Août',     '09' => 'Septembre',
        '10' => 'Octobre',   '11' => 'Novembre',  '12' => 'Décembre',
    );

    foreach ( $annees as $annee ) {
        foreach ( $mois_noms as $num => $nom ) {
            wp_insert_term(
                "$nom $annee",
                'mois_lecture',
                array( 'slug' => "$annee-$num" )
            );
        }
    }

    update_option( 'mois_lecture_created', true );
}


/* =========================================================================
 * 2. ACF FIELD GROUP: BILAN TRIMESTRIEL
 * ========================================================================= */

/**
 * Register the quarterly bilan field group.
 *
 * Adds 3 select fields (mois_1, mois_2, mois_3) to posts in the "bilan"
 * category. Each field is populated dynamically with reading months
 * (see section 4 below).
 *
 * The category ID is fetched dynamically by slug to avoid hardcoded IDs
 * that would break on migration.
 */
add_action( 'acf/init', 'tp_create_acf_bilan_fields' );
function tp_create_acf_bilan_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // Dynamic category ID lookup (avoids hardcoded ID)
    $bilan_cat = get_category_by_slug( 'bilan' );
    $bilan_id  = $bilan_cat ? $bilan_cat->term_id : 0;

    if ( ! $bilan_id ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'    => 'group_bilan_trimestriel',
        'title'  => 'Configuration du Bilan Trimestriel',
        'fields' => array(
            array(
                'key'           => 'field_mois_1',
                'label'         => 'Mois 1 du trimestre',
                'name'          => 'mois_1',
                'type'          => 'select',
                'instructions'  => 'Sélectionnez le premier mois',
                'required'      => 1,
                'choices'       => array(),
                'default_value' => '',
                'allow_null'    => 0,
                'ui'            => 1,
                'return_format' => 'value',
            ),
            array(
                'key'           => 'field_mois_2',
                'label'         => 'Mois 2 du trimestre',
                'name'          => 'mois_2',
                'type'          => 'select',
                'instructions'  => 'Sélectionnez le deuxième mois',
                'required'      => 1,
                'choices'       => array(),
                'default_value' => '',
                'allow_null'    => 0,
                'ui'            => 1,
                'return_format' => 'value',
            ),
            array(
                'key'           => 'field_mois_3',
                'label'         => 'Mois 3 du trimestre',
                'name'          => 'mois_3',
                'type'          => 'select',
                'instructions'  => 'Sélectionnez le troisième mois',
                'required'      => 1,
                'choices'       => array(),
                'default_value' => '',
                'allow_null'    => 0,
                'ui'            => 1,
                'return_format' => 'value',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_category',
                    'operator' => '==',
                    'value'    => $bilan_id,
                ),
            ),
        ),
    ) );
}


/* =========================================================================
 * 3. ACF FIELD: COUP DE COEUR (Chroniques)
 * ========================================================================= */

/**
 * Add a "Coup de coeur" toggle to chroniques.
 *
 * Displays as a toggle switch in the sidebar of the chronique editor.
 * Used to mark standout reviews, which are then highlighted in the
 * quarterly bilan reports and potentially on the front page.
 *
 * Returns a boolean (true/false), stored as 1/0 in the database.
 */
add_action( 'acf/init', 'tp_create_acf_coup_de_coeur_field' );
function tp_create_acf_coup_de_coeur_field() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'    => 'group_coup_de_coeur',
        'title'  => 'Coup de cœur',
        'fields' => array(
            array(
                'key'           => 'field_coup_de_coeur',
                'label'         => '💚 Coup de cœur',
                'name'          => 'coup_de_coeur',
                'type'          => 'true_false',
                'instructions'  => 'Cochez cette case si ce livre est un coup de cœur',
                'required'      => 0,
                'default_value' => 0,
                'ui'            => 1,
                'ui_on_text'    => 'Oui',
                'ui_off_text'   => 'Non',
                'return_format' => 'boolean',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'chroniques',
                ),
            ),
        ),
        'menu_order'            => 5,
        'position'              => 'side',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ) );
}


/* =========================================================================
 * 4. DYNAMIC MONTH CHOICES FOR BILAN FIELDS
 * ========================================================================= */

/**
 * Populate the month select fields with terms from 'mois_lecture' taxonomy.
 *
 * Hooked on each field individually via ACF's acf/load_field filter.
 * Terms are ordered by slug DESC so the most recent months appear first.
 */
function tp_load_mois_lecture_choices( $field ) {
    $field['choices'] = array();

    $terms = get_terms( array(
        'taxonomy'   => 'mois_lecture',
        'hide_empty' => false,
        'orderby'    => 'slug',
        'order'      => 'DESC',
    ) );

    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        foreach ( $terms as $term ) {
            $field['choices'][ $term->slug ] = $term->name;
        }
    } else {
        $field['choices'][''] = '⚠️ Aucun mois disponible';
    }

    return $field;
}

add_filter( 'acf/load_field/key=field_mois_1', 'tp_load_mois_lecture_choices' );
add_filter( 'acf/load_field/key=field_mois_2', 'tp_load_mois_lecture_choices' );
add_filter( 'acf/load_field/key=field_mois_3', 'tp_load_mois_lecture_choices' );


/* =========================================================================
 * 5. COUPS DE COEUR RETRIEVAL (for bilan templates)
 * ========================================================================= */

/**
 * Retrieve coups de coeur for a given quarter.
 *
 * Queries chroniques that are both marked as "coup de coeur" (ACF field)
 * AND assigned to the specified reading months (taxonomy).
 *
 * @param  array $mois_trimestre  Array of month slugs (e.g. '2024-01', '2024-02', '2024-03').
 * @return array                  Associative array keyed by month slug, each containing
 *                                an array of book data (id, title, author, rating, cover).
 */
function tp_get_coups_de_coeur_trimestre( $mois_trimestre ) {
    $coups_de_coeur = array();

    // Initialize empty array for each month
    foreach ( $mois_trimestre as $mois ) {
        $coups_de_coeur[ $mois ] = array();
    }

    foreach ( $mois_trimestre as $mois_slug ) {
        $args = array(
            'post_type'      => 'chroniques',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'coup_de_coeur',
                    'value'   => '1',
                    'compare' => '=',
                ),
            ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'mois_lecture',
                    'field'    => 'slug',
                    'terms'    => $mois_slug,
                ),
            ),
            'orderby' => 'date',
            'order'   => 'ASC',
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $post_id      = get_the_ID();
                $auteur_terms = wp_get_post_terms( $post_id, 'auteur', array( 'fields' => 'names' ) );
                $auteur       = ! empty( $auteur_terms ) ? $auteur_terms[0] : 'Auteur inconnu';
                $note         = get_post_meta( $post_id, 'note_etoiles', true );

                $coups_de_coeur[ $mois_slug ][] = array(
                    'id'         => $post_id,
                    'permalink'  => get_permalink(),
                    'titre'      => esc_html( get_the_title() ),
                    'auteur'     => esc_html( $auteur ),
                    'note'       => $note ? floatval( $note ) : null,
                    'couverture' => get_the_post_thumbnail_url( $post_id, 'medium' ),
                );
            }
            wp_reset_postdata();
        }
    }

    return $coups_de_coeur;
}