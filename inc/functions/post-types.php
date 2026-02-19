<?php
/**
 * ============================================================
 * 📚 CHRONIQUES — Custom Post Type + Taxonomies + Meta Boxes
 * ============================================================
 * Ce fichier gère tout ce qui concerne les "Chroniques littéraires" :
 * - Déclaration du Custom Post Type
 * - Taxonomies personnalisées
 * - Champs personnalisés (Année, Pages)
 * - Redirection automatique vers l’archive
 * - Modèle Gutenberg (optionnel)
 */

// ===============================
// 🧩 1. Custom Post Type : CHRONIQUES
// ===============================
function create_chroniques_post_type() {
    register_post_type('chroniques', array(
        'labels' => array(
            'name' => 'Chroniques',
            'singular_name' => 'Chronique',
            'add_new' => 'Ajouter une chronique',
            'add_new_item' => 'Ajouter une nouvelle chronique',
            'edit_item' => 'Modifier la chronique',
            'new_item' => 'Nouvelle chronique',
            'view_item' => 'Voir la chronique',
            'search_items' => 'Rechercher une chronique',
            'not_found' => 'Aucune chronique trouvée',
            'not_found_in_trash' => 'Aucune chronique dans la corbeille',
        ),
        'public' => true,
        'has_archive' => false,
        'menu_position' => 5,
        'rewrite' => array('slug' => 'chroniques'),
        'menu_icon' => 'dashicons-edit-page',
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'template' => array(
            // Résumé
            array('core/heading', array(
                'level' => 2, 
                'content' => 'Résumé'
            )),
            array('core/paragraph', array(
                'placeholder' => 'Écris ici ton résumé...'
            )),
            
            // Impressions
            array('core/heading', array(
                'level' => 2, 
                'content' => 'Impressions'
            )),
            array('core/paragraph', array(
                'placeholder' => 'Écris ici tes impressions sur le livre...'
            )),
            /*
            // Avis avec SPOILER
            array('core/heading', array(
                'level' => 2, 
                'content' => 'Avis avec SPOILER'
            )),
            array('core/details', array(
                'summary' => 'Clique ici pour te faire spoiler',
                'showContent' => false
            ), array(
                array('core/paragraph', array(
                    'placeholder' => 'Écris ici ton avis détaillé avec spoilers...'
                ))
            )),
            */
        ), 
        'template_lock' => false, // permet de modifier la structure dans l'éditeur
    ));
}
add_action('init', 'create_chroniques_post_type');

// ===============================
// 🧭 2. Taxonomies Personnalisées
// ===============================
function create_chroniques_taxonomies() {
    // Auteur
    register_taxonomy('auteur', 'chroniques', array(
        'label' => 'Auteur',
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ));

    // Nationalité
    register_taxonomy('nationalite', ['chroniques', 'artiste', 'post'], array(
        'label' => 'Nationalité',
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ));

    // Genre et sous-genre
    register_taxonomy('genre', 'chroniques', array(
        'label' => 'Genre',
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ));

    // Thèmes
    register_taxonomy('theme', ['chroniques', 'artiste', 'post'], array(
        'label' => 'Thèmes',
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ));

    // Période
    register_taxonomy('periode', 'chroniques', array(
        'label' => 'Période',
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ));

    // Mois de lecture (disponible pour CHRONIQUES et ARTICLES)
    register_taxonomy('mois_lecture', 'chroniques', array(
        'label' => 'Lu en',
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'show_in_menu' => true,
        'meta_box_cb' => 'post_categories_meta_box',
    ));

    // type de média
    register_taxonomy('type_media', 'chroniques', array(
        'labels' => array(
            'name' => 'Types de média',
            'singular_name' => 'Type de média',
            'search_items' => 'Rechercher un type',
            'all_items' => 'Tous les types',
            'edit_item' => 'Modifier le type',
            'update_item' => 'Mettre à jour',
            'add_new_item' => 'Ajouter un type',
            'new_item_name' => 'Nouveau type',
            'menu_name' => 'Types de média',
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'type-media'),
    ));
}
add_action('init', 'create_chroniques_taxonomies');

// ===============================
// 🗓️ 3. Champs Personnalisés (Meta Box)
// ===============================
function chroniques_add_meta_box() {
    add_meta_box(
        'chronique_details',
        'Détails',
        'chroniques_meta_box_html',
        'chroniques',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'chroniques_add_meta_box');

function chroniques_meta_box_html($post) {
    wp_nonce_field('chroniques_meta_save', 'chroniques_meta_nonce');

    $note = get_post_meta($post->ID, 'note_etoiles', true);
    $date_pub = get_post_meta($post->ID, 'date_publication', true);
    $date_sortie = get_post_meta($post->ID, 'date_sortie', true);
    $pages = get_post_meta($post->ID, 'pages', true);
    $saisons = get_post_meta($post->ID, 'saisons', true);
    $duree = get_post_meta($post->ID, 'duree', true);
    $duree_episode = get_post_meta($post->ID, 'duree_episode', true);
    $heures = get_post_meta($post->ID, 'heures_ecoute', true);
    $note = get_post_meta($post->ID, 'note_etoiles', true);
    ?>
    <p>
        <label><strong>Année de publication :</strong></label><br>
        <input type="number" name="date_publication"
               value="<?php echo esc_attr($date_pub); ?>"
               min="1700" max="2100" placeholder="Ex: 2024"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Année de sortie :</strong></label><br>
        <input type="number" name="date_sortie"
               value="<?php echo esc_attr($date_sortie); ?>"
               min="1700" max="2100" placeholder="Ex: 2024"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Nombre de pages :</strong></label><br>
        <input type="number" name="pages"
               value="<?php echo esc_attr($pages); ?>"
               min="0" placeholder="Ex: 350"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Nombre de saisons :</strong></label><br>
        <input type="number" name="saisons"
               value="<?php echo esc_attr($saisons); ?>"
               min="0" placeholder="Ex: 350"
               style="width:200px;">
    </p>
    <p>
        <label><strong>Durée de la vidéo :</strong></label><br>
        <input type="number" name="duree"
                value="<?php echo esc_attr($duree); ?>"
                min="0"
                placeholder="Durée en minutes (ex : 104)"
                style="width:200px;">
    </p>
    <p>
        <label><strong>Durée par épisode :</strong></label><br>
        <input type="number" name="duree_episode"
                value="<?php echo esc_attr($duree_episode); ?>"
                min="0"
                placeholder="Durée en minutes (ex : 104)"
                style="width:200px;">
    </p>
    <p>
        <label><strong>Heures d'écoute (si livre audio) :</strong></label><br>
        <input type="number" name="heures_ecoute"
               value="<?php echo esc_attr($heures); ?>"
               min="0" step="0.5" placeholder="Ex: 8.5"
               style="width:200px;">
        <small>Laisser vide si livre papier/numérique</small>
    </p>
    <p>
        <label><strong>Ma note :</strong></label><br>
        <select name="note_etoiles" style="width:200px;">
            <option value="">-- Non noté --</option>
            <?php for ($i = 0.5; $i <= 5; $i += 0.5): ?>
                <option value="<?php echo $i; ?>" <?php selected($note, $i); ?>>
                    <?php echo $i; ?> / 5
                </option>
            <?php endfor; ?>
        </select>
    </p>
    <?php
}

function chroniques_save_meta_data($post_id) {
    // Vérifier l'autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Vérifier le nonce
    if (!isset($_POST['chroniques_meta_nonce']) || 
        !wp_verify_nonce($_POST['chroniques_meta_nonce'], 'chroniques_meta_save')) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Vérifier le type de post
    if (get_post_type($post_id) !== 'chroniques') {
        return;
    }
    
    // Sauvegarder avec la bonne sanitization
    if (isset($_POST['note_etoiles'])) {
        $note = sanitize_text_field($_POST['note_etoiles']);
        // Valider que c'est un nombre valide entre 0 et 5
        if ($note === '' || (is_numeric($note) && $note >= 0 && $note <= 5)) {
            update_post_meta($post_id, 'note_etoiles', $note);
        }
    }
    
    if (isset($_POST['date_publication'])) {
        $year = absint($_POST['date_publication']); // Nombre entier positif
        if ($year >= 1700 && $year <= 2100) {
            update_post_meta($post_id, 'date_publication', $year);
        }
    }

    if (isset($_POST['date_sortie'])) {
        $year = absint($_POST['date_sortie']);

        if ($year >= 1700 && $year <= 2100) {
            update_post_meta($post_id, 'date_sortie', $year);
        }
    }
    
    if (isset($_POST['pages'])) {
        $pages = absint($_POST['pages']);
        update_post_meta($post_id, 'pages', $pages);
    }

    if (isset($_POST['saisons'])) {
        $saisons = absint($_POST['saisons']);
        update_post_meta($post_id, 'saisons', $saisons);
    }

    if (isset($_POST['duree'])) {
        if ($_POST['duree'] !== '') {
            update_post_meta($post_id, 'duree', absint($_POST['duree']));
        } else {
            delete_post_meta($post_id, 'duree');
        }
    }

    if (isset($_POST['duree_episode'])) {
        $duree_episode = absint($_POST['duree_episode']);
        update_post_meta($post_id, 'duree_episode', $duree_episode);
    }

    
    if (isset($_POST['heures_ecoute'])) {
        $heures = sanitize_text_field($_POST['heures_ecoute']);
        // Valider que c'est un nombre positif
        if ($heures === '' || (is_numeric($heures) && $heures >= 0)) {
            update_post_meta($post_id, 'heures_ecoute', $heures);
        }
    }
    
    if (isset($_POST['sexe_auteur'])) {
        $sexe = sanitize_text_field($_POST['sexe_auteur']);
        $allowed = array('femme', 'homme', 'autre', '');
        if (in_array($sexe, $allowed, true)) {
            update_post_meta($post_id, 'sexe_auteur', $sexe);
        }
    }
}
add_action('save_post', 'chroniques_save_meta_data');

// Auto-extrait
function chroniques_auto_excerpt($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'chroniques') return;
    
    $post = get_post($post_id);
    if (!empty($post->post_excerpt)) return;
    
    $content = $post->post_content;
    $blocks = parse_blocks($content);
    $first_paragraph = '';
    
    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/paragraph') {
            $first_paragraph = strip_tags(implode("\n", $block['innerContent']));
            break;
        }
    }
    
    if ($first_paragraph) {
        $words = wp_trim_words($first_paragraph, 40, '...');
        remove_action('save_post', 'chroniques_auto_excerpt');
        wp_update_post(array('ID' => $post_id, 'post_excerpt' => $words));
        add_action('save_post', 'chroniques_auto_excerpt');
    }
}
add_action('save_post', 'chroniques_auto_excerpt');

// Script compteur d'extrait
function chroniques_excerpt_counter_enqueue($hook) {
    if ('post.php' !== $hook && 'post-new.php' !== $hook) return;
    wp_enqueue_script(
        'chroniques-excerpt-counter',
        get_stylesheet_directory_uri() . '/assets/js/modules/excerpt-counter.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'chroniques_excerpt_counter_enqueue');

// ===============================
// 🗂 4. Meta Box Spoiler
// ===============================
function chroniques_add_spoiler_meta_box() {
    add_meta_box(
        'chroniques_spoiler',            // ID
        'Avis avec SPOILER',             // Titre
        'chroniques_spoiler_meta_box_html',  // Fonction d'affichage
        'chroniques',                    // Post type
        'normal',                        // Position
        'default'                        // Priorité
    );
}
add_action('add_meta_boxes', 'chroniques_add_spoiler_meta_box');

function chroniques_spoiler_meta_box_html($post) {
    wp_nonce_field('chroniques_spoiler_save', 'chroniques_spoiler_nonce');

    $spoiler = get_post_meta($post->ID, '_chroniques_spoiler', true);

    wp_editor($spoiler, 'chroniques_spoiler_editor', array(
        'textarea_name' => 'chroniques_spoiler',
        'media_buttons' => false,
        'textarea_rows' => 8
    ));
}

// Sauvegarde du spoiler
function chroniques_save_spoiler($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['chroniques_spoiler_nonce']) || 
        !wp_verify_nonce($_POST['chroniques_spoiler_nonce'], 'chroniques_spoiler_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['chroniques_spoiler'])) {
        update_post_meta($post_id, '_chroniques_spoiler', wp_kses_post($_POST['chroniques_spoiler']));
    }
}
add_action('save_post', 'chroniques_save_spoiler');
