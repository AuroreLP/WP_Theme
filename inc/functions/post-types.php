<?php
/**
 * Chroniques — Custom Post Type, Taxonomies & Meta Boxes
 *
 * This file defines everything related to the "Chroniques" content type:
 * literary, film, series, and podcast reviews. It is the main content
 * pillar of L'Ivresse des Mots alongside standard posts (articles)
 * and the 'artiste' CPT managed by Pods.
 *
 * Structure:
 * 1. CPT registration (chroniques)
 * 2. Custom taxonomies (auteur, nationalité, genre, thème, période, etc.)
 * 3. Meta boxes — Details (publication year, pages, rating, etc.)
 * 4. Meta box — Spoiler section
 * 5. Meta box — Sources (shared across chroniques, posts, and artistes)
 * 6. Auto-excerpt generation from first paragraph
 * 7. Admin scripts (excerpt counter)
 * 8. Artiste CPT slug rewrite (Pods override)
 *
 * @package turningpages
 */


/* =========================================================================
 * 1. CUSTOM POST TYPE: CHRONIQUES
 * ========================================================================= */

/**
 * Register the 'chroniques' post type.
 *
 * This is a manually coded CPT (not via Pods) to keep full control over
 * the registration args, Gutenberg template, and meta boxes.
 *
 * Key choices:
 * - has_archive => false: Chroniques are listed via a custom page template
 *   (page-chroniques.php) with filters, not WP's default archive.
 * - show_in_rest => true: Required for Gutenberg editor support.
 * - template: Pre-fills new chroniques with Résumé/Impressions headings
 *   to guide the writing structure. template_lock is false so the
 *   structure can be modified per post if needed.
 */
add_action( 'init', 'tp_register_chroniques_cpt' );
function tp_register_chroniques_cpt() {
    register_post_type( 'chroniques', array(
        'labels' => array(
            'name'               => 'Chroniques',
            'singular_name'      => 'Chronique',
            'add_new'            => 'Ajouter une chronique',
            'add_new_item'       => 'Ajouter une nouvelle chronique',
            'edit_item'          => 'Modifier la chronique',
            'new_item'           => 'Nouvelle chronique',
            'view_item'          => 'Voir la chronique',
            'search_items'       => 'Rechercher une chronique',
            'not_found'          => 'Aucune chronique trouvée',
            'not_found_in_trash' => 'Aucune chronique dans la corbeille',
        ),
        'public'        => true,
        'has_archive'   => false,
        'menu_position' => 5,
        'rewrite'       => array( 'slug' => 'chroniques' ),
        'menu_icon'     => 'dashicons-edit-page',
        'show_in_rest'  => true,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),

        // Gutenberg block template — pre-fills the editor with a structure
        'template' => array(
            array( 'core/heading', array(
                'level'   => 2,
                'content' => 'Résumé',
            ) ),
            array( 'core/paragraph', array(
                'placeholder' => 'Écris ici ton résumé...',
            ) ),
            array( 'core/heading', array(
                'level'   => 2,
                'content' => 'Impressions',
            ) ),
            array( 'core/paragraph', array(
                'placeholder' => 'Écris ici tes impressions sur le livre...',
            ) ),
        ),
        'template_lock' => false,
    ) );
}


/* =========================================================================
 * 2. CUSTOM TAXONOMIES
 * ========================================================================= */

/**
 * Register all taxonomies attached to the chroniques CPT.
 *
 * Some taxonomies are shared with other post types:
 * - 'nationalite' and 'theme': shared with 'artiste' and 'post'
 * - 'mois_lecture': chroniques only (used for quarterly bilan reports)
 *
 * Hierarchical taxonomies (genre, periode, type_media) behave like
 * categories (parent/child). Non-hierarchical ones behave like tags.
 */
add_action( 'init', 'tp_register_chroniques_taxonomies' );
function tp_register_chroniques_taxonomies() {

    // Author of the reviewed work (not the blog author)
    register_taxonomy( 'auteur', 'chroniques', array(
        'label'             => 'Auteur',
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );

    // Nationality — shared across chroniques, artistes, and articles
    register_taxonomy( 'nationalite', array( 'chroniques', 'artiste', 'post' ), array(
        'label'             => 'Nationalité',
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );

    // Genre (hierarchical — supports parent/child, e.g. Fiction > Science-fiction)
    register_taxonomy( 'genre', 'chroniques', array(
        'label'             => 'Genre',
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );

    // Themes — shared across chroniques, artistes, and articles
    register_taxonomy( 'theme', array( 'chroniques', 'artiste', 'post' ), array(
        'label'             => 'Thèmes',
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );

    // Historical period (hierarchical)
    register_taxonomy( 'periode', 'chroniques', array(
        'label'             => 'Période',
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );

    /**
     * Month of reading — used to group chroniques by month for bilan reports.
     * meta_box_cb => 'post_categories_meta_box' forces a checkbox UI in the
     * editor instead of the default tag-style input, even though it's
     * non-hierarchical. This makes it easier to select months.
     */
    register_taxonomy( 'mois_lecture', 'chroniques', array(
        'label'             => 'Lu en',
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'show_in_menu'      => true,
        'meta_box_cb'       => 'post_categories_meta_box',
    ) );

    // Media type (livre, film, série, podcast) — hierarchical for clean grouping
    register_taxonomy( 'type_media', 'chroniques', array(
        'labels' => array(
            'name'          => 'Types de média',
            'singular_name' => 'Type de média',
            'search_items'  => 'Rechercher un type',
            'all_items'     => 'Tous les types',
            'edit_item'     => 'Modifier le type',
            'update_item'   => 'Mettre à jour',
            'add_new_item'  => 'Ajouter un type',
            'new_item_name' => 'Nouveau type',
            'menu_name'     => 'Types de média',
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'type-media' ),
    ) );
}


/* =========================================================================
 * 3. META BOX: CHRONIQUE DETAILS
 * ========================================================================= */

/**
 * Register the "Détails" meta box for chroniques.
 *
 * Contains fields that vary by media type:
 * - Books: publication year, pages, listening hours (audiobook)
 * - Films: release year, duration
 * - Series: release year, seasons, episode duration
 * - Podcasts: listening hours
 * - All types: star rating (0.5 to 5)
 *
 * Fields are displayed in the admin editor below the main content area.
 * Irrelevant fields for a given media type are simply left empty.
 */
add_action( 'add_meta_boxes', 'tp_add_chronique_details_meta_box' );
function tp_add_chronique_details_meta_box() {
    add_meta_box(
        'chronique_details',
        'Détails',
        'tp_chronique_details_html',
        'chroniques',
        'normal',
        'high'
    );
}

/**
 * Render the details meta box fields.
 */
function tp_chronique_details_html( $post ) {
    wp_nonce_field( 'chroniques_meta_save', 'chroniques_meta_nonce' );

    // Retrieve all meta values at once
    $date_pub      = get_post_meta( $post->ID, 'date_publication', true );
    $date_sortie   = get_post_meta( $post->ID, 'date_sortie', true );
    $pages         = get_post_meta( $post->ID, 'pages', true );
    $saisons       = get_post_meta( $post->ID, 'saisons', true );
    $duree         = get_post_meta( $post->ID, 'duree', true );
    $duree_episode = get_post_meta( $post->ID, 'duree_episode', true );
    $heures        = get_post_meta( $post->ID, 'heures_ecoute', true );
    $note          = get_post_meta( $post->ID, 'note_etoiles', true );
    ?>

    <p>
        <label><strong>Année de publication :</strong></label><br>
        <input type="number" name="date_publication"
               value="<?php echo esc_attr( $date_pub ); ?>"
               min="1700" max="2100" placeholder="Ex: 2024"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Année de sortie :</strong></label><br>
        <input type="number" name="date_sortie"
               value="<?php echo esc_attr( $date_sortie ); ?>"
               min="1700" max="2100" placeholder="Ex: 2024"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Nombre de pages :</strong></label><br>
        <input type="number" name="pages"
               value="<?php echo esc_attr( $pages ); ?>"
               min="0" placeholder="Ex: 350"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Nombre de saisons :</strong></label><br>
        <input type="number" name="saisons"
               value="<?php echo esc_attr( $saisons ); ?>"
               min="0" placeholder="Ex: 3"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Durée de la vidéo :</strong></label><br>
        <input type="number" name="duree"
               value="<?php echo esc_attr( $duree ); ?>"
               min="0" placeholder="Durée en minutes (ex : 104)"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Durée par épisode :</strong></label><br>
        <input type="number" name="duree_episode"
               value="<?php echo esc_attr( $duree_episode ); ?>"
               min="0" placeholder="Durée en minutes (ex : 45)"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Heures d'écoute (si livre audio) :</strong></label><br>
        <input type="number" name="heures_ecoute"
               value="<?php echo esc_attr( $heures ); ?>"
               min="0" step="0.5" placeholder="Ex: 8.5"
               style="width:200px;">
        <small>Laisser vide si livre papier/numérique</small>
    </p>
    <p>
        <label><strong>Ma note :</strong></label><br>
        <select name="note_etoiles" style="width:200px;">
            <option value="">-- Non noté --</option>
            <?php for ( $i = 0.5; $i <= 5; $i += 0.5 ) : ?>
                <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $note, $i ); ?>>
                    <?php echo esc_html( $i ); ?> / 5
                </option>
            <?php endfor; ?>
        </select>
    </p>
    <?php
}

/**
 * Save the details meta box data.
 *
 * Security checks (in order):
 * 1. Skip autosave (would lose data from unchecked fields)
 * 2. Verify nonce (CSRF protection)
 * 3. Check user capabilities
 * 4. Confirm post type
 *
 * Sanitization strategy:
 * - Numeric fields use absint() for integers, floats are validated manually
 * - Empty fields delete the meta entirely (avoids storing "0" for unset fields)
 * - Constrained fields (note, year) are validated against allowed ranges
 */
add_action( 'save_post', 'tp_save_chronique_details' );
function tp_save_chronique_details( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['chroniques_meta_nonce'] ) ||
         ! wp_verify_nonce( $_POST['chroniques_meta_nonce'], 'chroniques_meta_save' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'chroniques' ) {
        return;
    }

    /**
     * Helper: save or delete a numeric meta field.
     * Stores the value if non-empty and valid, deletes otherwise.
     * This prevents storing "0" for fields the user intentionally left blank.
     */
    $save_numeric = function ( $field, $meta_key, $min = 0, $max = null ) use ( $post_id ) {
        if ( ! isset( $_POST[ $field ] ) || $_POST[ $field ] === '' ) {
            delete_post_meta( $post_id, $meta_key );
            return;
        }
        $value = absint( $_POST[ $field ] );
        if ( $value >= $min && ( $max === null || $value <= $max ) ) {
            update_post_meta( $post_id, $meta_key, $value );
        }
    };

    // Year fields (constrained range)
    $save_numeric( 'date_publication', 'date_publication', 1700, 2100 );
    $save_numeric( 'date_sortie', 'date_sortie', 1700, 2100 );

    // Integer fields (zero or positive)
    $save_numeric( 'pages', 'pages' );
    $save_numeric( 'saisons', 'saisons' );
    $save_numeric( 'duree', 'duree' );
    $save_numeric( 'duree_episode', 'duree_episode' );

    // Listening hours — float (0.5 increments)
    if ( isset( $_POST['heures_ecoute'] ) ) {
        if ( $_POST['heures_ecoute'] === '' ) {
            delete_post_meta( $post_id, 'heures_ecoute' );
        } else {
            $heures = floatval( $_POST['heures_ecoute'] );
            if ( $heures >= 0 ) {
                update_post_meta( $post_id, 'heures_ecoute', sanitize_text_field( $heures ) );
            }
        }
    }

    // Star rating — float between 0.5 and 5
    if ( isset( $_POST['note_etoiles'] ) ) {
        if ( $_POST['note_etoiles'] === '' ) {
            delete_post_meta( $post_id, 'note_etoiles' );
        } else {
            $note = floatval( $_POST['note_etoiles'] );
            if ( $note >= 0.5 && $note <= 5 ) {
                update_post_meta( $post_id, 'note_etoiles', $note );
            }
        }
    }
}


/* =========================================================================
 * 4. META BOX: SPOILER SECTION
 * ========================================================================= */

/**
 * Optional spoiler content — displayed behind a toggle on the front end.
 *
 * Uses a plain <textarea> instead of wp_editor() for simplicity.
 * The spoiler content supports basic HTML via wp_kses_post() on save.
 *
 * Stored as _chroniques_spoiler (underscore prefix = hidden from
 * the default Custom Fields panel in the editor).
 */
add_action( 'add_meta_boxes', 'tp_add_spoiler_meta_box' );
function tp_add_spoiler_meta_box() {
    add_meta_box(
        'chroniques_spoiler',
        'Avis avec SPOILER',
        'tp_spoiler_meta_box_html',
        'chroniques',
        'normal',
        'default'
    );
}

function tp_spoiler_meta_box_html( $post ) {
    wp_nonce_field( 'chroniques_spoiler_save', 'chroniques_spoiler_nonce' );
    $spoiler = get_post_meta( $post->ID, '_chroniques_spoiler', true );
    ?>
    <textarea name="chroniques_spoiler" id="chroniques_spoiler" rows="8" style="width:100%;"><?php echo esc_textarea( $spoiler ); ?></textarea>
    <?php
}

add_action( 'save_post', 'tp_save_spoiler' );
function tp_save_spoiler( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['chroniques_spoiler_nonce'] ) ||
         ! wp_verify_nonce( $_POST['chroniques_spoiler_nonce'], 'chroniques_spoiler_save' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['chroniques_spoiler'] ) ) {
        update_post_meta( $post_id, '_chroniques_spoiler', wp_kses_post( $_POST['chroniques_spoiler'] ) );
    }
}


/* =========================================================================
 * 5. META BOX: SOURCES (shared across post types)
 * ========================================================================= */

/**
 * Sources meta box — available on chroniques, articles, and artiste profiles.
 *
 * Allows listing references, interviews, and documentation used to write
 * the content. One source per line, plain text with basic HTML allowed.
 *
 * Stored as _post_sources (underscore prefix = hidden from Custom Fields).
 */
add_action( 'add_meta_boxes', 'tp_add_sources_meta_box' );
function tp_add_sources_meta_box() {
    $post_types = array( 'chroniques', 'post', 'artiste' );
    foreach ( $post_types as $type ) {
        add_meta_box(
            'sources_meta_box',
            'Sources',
            'tp_sources_meta_box_html',
            $type,
            'normal',
            'low'
        );
    }
}

function tp_sources_meta_box_html( $post ) {
    wp_nonce_field( 'sources_meta_save', 'sources_meta_nonce' );
    $sources = get_post_meta( $post->ID, '_post_sources', true );
    ?>
    <p><small>Une source par ligne. Ex : Antoine Chevrollier, interview Cineuropa (mai 2024)</small></p>
    <textarea name="post_sources" id="post_sources" rows="6" style="width:100%;"><?php echo esc_textarea( $sources ); ?></textarea>
    <?php
}

add_action( 'save_post', 'tp_save_sources' );
function tp_save_sources( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['sources_meta_nonce'] ) ||
         ! wp_verify_nonce( $_POST['sources_meta_nonce'], 'sources_meta_save' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['post_sources'] ) ) {
        update_post_meta( $post_id, '_post_sources', wp_kses_post( $_POST['post_sources'] ) );
    }
}


/* =========================================================================
 * 6. AUTO-EXCERPT GENERATION
 * ========================================================================= */

/**
 * Automatically generate an excerpt from the first paragraph if none is set.
 *
 * When a chronique is saved without a manual excerpt, this parses the
 * Gutenberg blocks to find the first core/paragraph and extracts up to
 * 40 words as the excerpt.
 *
 * The remove/add_action dance prevents an infinite loop: wp_update_post()
 * triggers save_post again, which would call this function recursively.
 */
add_action( 'save_post', 'tp_chroniques_auto_excerpt' );
function tp_chroniques_auto_excerpt( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'chroniques' ) {
        return;
    }

    $post = get_post( $post_id );

    // Don't overwrite a manually written excerpt
    if ( ! empty( $post->post_excerpt ) ) {
        return;
    }

    $blocks          = parse_blocks( $post->post_content );
    $first_paragraph = '';

    foreach ( $blocks as $block ) {
        if ( $block['blockName'] === 'core/paragraph' ) {
            $first_paragraph = strip_tags( implode( "\n", $block['innerContent'] ) );
            break;
        }
    }

    if ( $first_paragraph ) {
        $excerpt = wp_trim_words( $first_paragraph, 40, '...' );

        // Temporarily unhook to prevent infinite loop
        remove_action( 'save_post', 'tp_chroniques_auto_excerpt' );
        wp_update_post( array(
            'ID'           => $post_id,
            'post_excerpt' => $excerpt,
        ) );
        add_action( 'save_post', 'tp_chroniques_auto_excerpt' );
    }
}


/* =========================================================================
 * 7. ADMIN SCRIPTS — Excerpt Character Counter
 * ========================================================================= */

/**
 * Enqueue the excerpt counter script on post edit screens.
 *
 * This adds a live character count below the excerpt field in the editor.
 * Only loaded on post.php and post-new.php to avoid unnecessary admin bloat.
 *
 * NOTE: jQuery dependency is fine here — the WordPress admin always loads
 * jQuery, unlike the front end where we removed it.
 */
add_action( 'admin_enqueue_scripts', 'tp_enqueue_excerpt_counter' );
function tp_enqueue_excerpt_counter( $hook ) {
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    wp_enqueue_script(
        'chroniques-excerpt-counter',
        get_stylesheet_directory_uri() . '/assets/js/modules/excerpt-counter.js',
        array( 'jquery' ),
        tp_asset_version( 'assets/js/modules/excerpt-counter.js' ),
        true
    );
}


/* =========================================================================
 * 8. ARTISTE CPT — Slug Rewrite Override
 * ========================================================================= */

/**
 * Change the 'artiste' CPT URL slug from /artiste/ to /portrait/.
 *
 * The 'artiste' CPT is managed by Pods, which registers it with its own
 * slug. This filter intercepts the registration args and overrides the
 * rewrite slug AFTER Pods sets it up.
 *
 * Result: site.com/portrait/name instead of site.com/artiste/name
 *
 * This approach (filter on register_post_type_args) is preferred over
 * modifying Pods settings directly because:
 * - It's version-controlled in the theme
 * - It survives Pods configuration resets
 * - It's visible in the codebase
 *
 * IMPORTANT: After changing this, flush rewrite rules:
 * Settings > Permalinks > Save (no changes needed, just click Save).
 */
add_filter( 'register_post_type_args', 'tp_rewrite_artiste_slug', 10, 2 );
function tp_rewrite_artiste_slug( $args, $post_type ) {
    if ( 'artiste' === $post_type ) {
        $args['rewrite'] = array(
            'slug'       => 'portrait',
            'with_front' => false,
        );
    }
    return $args;
}
