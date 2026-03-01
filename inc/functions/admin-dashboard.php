<?php 
// ===============================
// 1. Widget "En un coup d'œil" — contenu publié uniquement
// ===============================
add_action( 'dashboard_glance_items', 'jerem_dashboard_cpt_glance_items' );

function jerem_dashboard_cpt_glance_items( $items ) {

    $post_types = array( 'chroniques', 'artiste' );

    foreach ( $post_types as $pt ) {

        $post_type_obj = get_post_type_object( $pt );

        if ( ! $post_type_obj ) {
            continue;
        }

        $counts    = wp_count_posts( $pt );
        $published = (int) $counts->publish;
        $label     = _n(
            $post_type_obj->labels->singular_name,
            $post_type_obj->labels->name,
            $published
        );

        $items[] = sprintf(
            '<a class="cpt-count-%s" href="%s">%d %s</a>',
            esc_attr( $pt ),
            esc_url( admin_url( 'edit.php?post_type=' . $pt ) ),
            $published,
            esc_html( $label )
        );
    }

    return $items;
}

// ===============================
// 2. Icônes Dashicons pour les CPT
// ===============================
add_action( 'admin_head', 'jerem_dashboard_cpt_icons' );

function jerem_dashboard_cpt_icons() {
    echo '<style>
        #dashboard_right_now a.cpt-count-chroniques::before { content: "\f330"; }
        #dashboard_right_now a.cpt-count-artiste::before    { content: "\f110"; }
    </style>';
}

// ===============================
// 3. Brouillons récents — inclut Articles, Chroniques et Portraits
// ===============================
add_action( 'wp_dashboard_setup', 'jerem_customize_quick_draft_widget' );

function jerem_customize_quick_draft_widget() {
    // On retire le widget original "Brouillon rapide"
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );

    // On le remet avec notre version personnalisée des brouillons
    wp_add_dashboard_widget(
        'dashboard_quick_press',
        __( 'Brouillon rapide' ),
        'jerem_custom_quick_draft_display'
    );
}

function jerem_custom_quick_draft_display() {
    // On garde le formulaire de brouillon rapide natif
    wp_quick_draft();

    // On remplace la liste des brouillons par la nôtre (tous les CPT)
    jerem_recent_drafts_all_cpt();
}

function jerem_recent_drafts_all_cpt() {

    $drafts = new WP_Query( array(
        'post_type'      => array( 'post', 'chroniques', 'artiste' ),
        'post_status'    => array( 'draft', 'pending' ),
        'posts_per_page' => 5,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true, // Performance : pas besoin de pagination
    ) );

    if ( ! $drafts->have_posts() ) {
        echo '<p>' . esc_html__( 'Aucun brouillon pour le moment.' ) . '</p>';
        return;
    }

    echo '<div class="drafts">';
    echo '<p class="sub">' . esc_html__( 'Vos brouillons récents' ) . '</p>';
    echo '<ul>';

    while ( $drafts->have_posts() ) {
        $drafts->the_post();

        $post_type_obj = get_post_type_object( get_post_type() );
        $type_label    = $post_type_obj ? $post_type_obj->labels->singular_name : '';
        $edit_link     = get_edit_post_link();
        $title         = get_the_title() ? get_the_title() : __( '(sans titre)' );
        $time          = get_the_modified_time( 'U' );
        $human_time    = human_time_diff( $time ) . ' ago';

        printf(
            '<li><a href="%s"><strong>%s</strong> <span class="drafts-type">[%s]</span></a> <abbr title="%s">%s</abbr></li>',
            esc_url( $edit_link ),
            esc_html( $title ),
            esc_html( $type_label ),
            esc_attr( get_the_modified_date() ),
            esc_html( $human_time )
        );
    }

    echo '</ul>';
    echo '</div>';

    wp_reset_postdata();
}

?>