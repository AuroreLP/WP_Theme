<?php
/**
 * Admin Dashboard Customizations
 *
 * Enhances the WordPress admin dashboard with:
 * 1. CPT counts in the "At a Glance" widget (chroniques + artistes)
 * 2. Custom dashicons for CPT counters
 * 3. Extended "Quick Draft" widget showing drafts from all content types
 *
 * @package turningpages
 */


/* =========================================================================
 * 1. "AT A GLANCE" WIDGET — Published CPT Counts
 * ========================================================================= */

/**
 * Add chroniques and artistes counts to the "At a Glance" dashboard widget.
 *
 * WordPress natively only shows posts, pages, and comments.
 * This adds a clickable count for each custom post type that links
 * directly to its admin listing page.
 */
add_action( 'dashboard_glance_items', 'tp_dashboard_cpt_glance_items' );
function tp_dashboard_cpt_glance_items( $items ) {
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


/* =========================================================================
 * 2. DASHICONS FOR CPT COUNTERS
 * ========================================================================= */

/**
 * Inject CSS for custom dashicons next to CPT counts.
 *
 * Uses WordPress Dashicons font codes:
 * - \f330 = edit-page icon (chroniques)
 * - \f110 = groups icon (artistes)
 */
add_action( 'admin_head', 'tp_dashboard_cpt_icons' );
function tp_dashboard_cpt_icons() {
    echo '<style>
        #dashboard_right_now a.cpt-count-chroniques::before { content: "\f330"; }
        #dashboard_right_now a.cpt-count-artiste::before    { content: "\f110"; }
    </style>';
}


/* =========================================================================
 * 3. EXTENDED QUICK DRAFT WIDGET — All Content Types
 * ========================================================================= */

/**
 * Replace the default "Quick Draft" widget with a custom version
 * that shows recent drafts from ALL content types (posts, chroniques,
 * artistes) instead of just standard posts.
 *
 * The native quick draft form is preserved — only the drafts list
 * below it is replaced with our multi-CPT version.
 */
add_action( 'wp_dashboard_setup', 'tp_customize_quick_draft_widget' );
function tp_customize_quick_draft_widget() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );

    wp_add_dashboard_widget(
        'dashboard_quick_press',
        __( 'Brouillon rapide' ),
        'tp_custom_quick_draft_display'
    );
}

/**
 * Render the custom quick draft widget content.
 * Keeps the native quick draft form + appends our multi-CPT drafts list.
 */
function tp_custom_quick_draft_display() {
    wp_quick_draft();
    tp_recent_drafts_all_cpt();
}

/**
 * Display recent drafts and pending posts across all content types.
 *
 * Shows the 5 most recently modified drafts with:
 * - Post title (linked to edit screen)
 * - Content type label in brackets
 * - Human-readable time since last modification
 *
 * Performance: no_found_rows => true skips the COUNT query since
 * we don't need pagination info for a fixed 5-item list.
 */
function tp_recent_drafts_all_cpt() {
    $drafts = new WP_Query( array(
        'post_type'      => array( 'post', 'chroniques', 'artiste' ),
        'post_status'    => array( 'draft', 'pending' ),
        'posts_per_page' => 5,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true,
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
        $human_time    = sprintf( 'il y a %s', human_time_diff( $time ) );

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