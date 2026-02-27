<?php 
/**
 * Ajoute les CPT (Chroniques & Portraits) au widget "En un coup d'œil" du dashboard.
 */
add_action( 'dashboard_glance_items', 'jerem_dashboard_cpt_glance_items' );

function jerem_dashboard_cpt_glance_items( $items ) {

    // Liste des CPT à afficher — adapte les slugs à tes vrais slugs
    $post_types = array( 'chroniques', 'artiste' );

    foreach ( $post_types as $pt ) {

        $post_type_obj = get_post_type_object( $pt );

        // Sécurité : on vérifie que le CPT existe bien
        if ( ! $post_type_obj ) {
            continue;
        }

        $counts = wp_count_posts( $pt );

        // --- Publiés ---
        $published = (int) $counts->publish;
        $label     = _n(
            $post_type_obj->labels->singular_name,
            $post_type_obj->labels->name,
            $published
        );
        // Lien vers la liste filtrée dans l'admin
        $items[] = sprintf(
            '<a class="cpt-count-%s" href="%s">%d %s</a>',
            esc_attr( $pt ),
            esc_url( admin_url( 'edit.php?post_type=' . $pt ) ),
            $published,
            esc_html( $label )
        );

        // --- Brouillons ---
        $drafts = (int) $counts->draft;
        if ( $drafts > 0 ) {
            $items[] = sprintf(
                '<a class="cpt-draft-%s" href="%s">%d %s en brouillon</a>',
                esc_attr( $pt ),
                esc_url( admin_url( 'edit.php?post_status=draft&post_type=' . $pt ) ),
                $drafts,
                esc_html( $post_type_obj->labels->singular_name )
            );
        }

        // --- En attente de relecture ---
        $pending = (int) $counts->pending;
        if ( $pending > 0 ) {
            $items[] = sprintf(
                '<a class="cpt-pending-%s" href="%s">%d %s en attente</a>',
                esc_attr( $pt ),
                esc_url( admin_url( 'edit.php?post_status=pending&post_type=' . $pt ) ),
                $pending,
                esc_html( $post_type_obj->labels->singular_name )
            );
        }
    }

    return $items;
}

add_action( 'admin_head', 'jerem_dashboard_cpt_icons' );

function jerem_dashboard_cpt_icons() {
    echo '<style>
        #dashboard_right_now a.cpt-count-chroniques::before { content: "\f330"; } /* dashicon book */
        #dashboard_right_now a.cpt-count-artiste::before    { content: "\f110"; } /* dashicon id-alt (portrait) */
    </style>';
}

?>