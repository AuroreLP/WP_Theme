<?php
/**
 * Fonctions helper pour afficher les taxonomies des chroniques et des articles
 */

/**
 * Récupère le genre à afficher (sous-genre en priorité, sinon genre parent)
 */
function get_chronique_genre_display() {
    $genres = get_the_terms(get_the_ID(), 'genre');
    
    if (!$genres || is_wp_error($genres)) {
        return null;
    }
    
    $genre_parent = null;
    $sous_genre = null;
    
    foreach ($genres as $genre) {
        if ($genre->parent == 0) {
            $genre_parent = $genre;
        } else {
            $sous_genre = $genre;
            break; // on prend seulement le premier sous-genre trouvé
        }
    }
    
    $genre_to_display = $sous_genre ? $sous_genre : $genre_parent;
    
    if ($genre_to_display) {
        return array(
            'name' => $genre_to_display->name,
            'slug' => $genre_to_display->slug,
            'link' => get_term_link($genre_to_display),
            'term' => $genre_to_display
        );
    }
    
    return null;
}

/**
 * Affiche les genres (sous-genres en priorité) en liste HTML avec liens vers archives
 */
function display_chronique_genres_list() {
    $genres = get_the_terms(get_the_ID(), 'genre');
    if (!$genres || is_wp_error($genres)) return;
    
    $genre_parent = null;
    $sous_genres = array();
    
    foreach ($genres as $genre) {
        if ($genre->parent == 0) {
            $genre_parent = $genre;
        } else {
            $sous_genres[] = $genre;
        }
    }
    
    if (!empty($sous_genres)) {
        foreach ($sous_genres as $sg) {
            echo '<li><a href="' . esc_url(get_term_link($sg)) . '">' . esc_html($sg->name) . '</a></li>';
        }
    } elseif ($genre_parent) {
        echo '<li><a href="' . esc_url(get_term_link($genre_parent)) . '">' . esc_html($genre_parent->name) . '</a></li>';
    }
}

/**
 * Affiche les thèmes en liste HTML avec liens vers archives
 */
function display_chronique_themes_list() {
    $themes = get_the_terms(get_the_ID(), 'theme');
    if (!$themes || is_wp_error($themes)) return;
    
    foreach ($themes as $theme) {
        echo '<li><a href="' . esc_url(get_term_link($theme)) . '">' . esc_html($theme->name) . '</a></li>';
    }
}

/**
 * Récupère les thèmes d'une chronique (pour filtres JS par ex.)
 */
function get_chronique_themes() {
    $themes = get_the_terms(get_the_ID(), 'theme'); 
    if (!$themes || is_wp_error($themes)) {
        return null;
    }
    return $themes;
}

/**
 * Affiche les auteurs en liste HTML avec liens vers archives
 */
function display_chronique_auteurs_list() {
    $auteurs = get_the_terms(get_the_ID(), 'auteur');
    if (!$auteurs || is_wp_error($auteurs)) return;

    foreach ($auteurs as $auteur) {
        echo '<li><a href="' . esc_url(get_term_link($auteur)) . '">' . esc_html($auteur->name) . '</a></li>';
    }
}

/**
 * Affiche les nationalités en liste HTML avec liens vers archives
 */
function display_chronique_nationalites_list() {
    $nationalites = get_the_terms(get_the_ID(), 'nationalite');
    if (!$nationalites || is_wp_error($nationalites)) return;

    foreach ($nationalites as $nation) {
        echo '<li><a href="' . esc_url(get_term_link($nation)) . '">' . esc_html($nation->name) . '</a></li>';
    }
}

/**
 * Affiche les rôle en liste HTML avec liens vers archives
 */
function display_chronique_roles_list() {
    $roles = get_the_terms(get_the_ID(), 'role');
    if (!$roles || is_wp_error($roles)) return;

    foreach ($roles as $role) {
        echo '<li><a href="' . esc_url(get_term_link($role)) . '">' . esc_html($role->name) . '</a></li>';
    }
}

/**
 * Affiche les tags en liste HTML avec liens vers archives
 */
function display_chronique_tags_list() {
    $tags = get_the_tags();
    if (!$tags || is_wp_error($tags)) return;

    foreach ($tags as $tag) {
        echo '<li><a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a></li>';
    }
}

/**
 * Récupère les tags d'une chronique (pour filtres JS par ex.)
 */
function get_chronique_tags() {
    $tags = get_the_tags();
    if (!$tags || is_wp_error($tags)) {
        return null;
    }
    return $tags;
}

/**
 * Affiche les tags en texte simple séparé par des virgules
 */
function display_chronique_tags_inline() {
    $tags = get_the_tags();
    if (!$tags || is_wp_error($tags)) return;
    
    $tag_names = array();
    foreach ($tags as $tag) {
        $tag_names[] = '<a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
    }
    echo implode(', ', $tag_names);
}

/**
 * Rank Math — Variable personnalisée %chronique_auteur%
 * Récupère le nom de l'auteur depuis la taxonomy "auteur"
 */
add_action('rank_math/vars/register_extra_replacements', function() {
    rank_math_register_var_replacement(
        'chronique_auteur',
        [
            'name'        => 'Auteur de la chronique',
            'description' => 'Affiche le nom de l\'auteur depuis la taxonomy auteur',
            'variable'    => 'chronique_auteur',
            'example'     => 'Stephen King',
        ],
        function() {
            global $post;
            if (!$post || $post->post_type !== 'chroniques') return '';

            $auteur_terms = get_the_terms($post->ID, 'auteur');
            if (!empty($auteur_terms) && !is_wp_error($auteur_terms)) {
                return implode(', ', wp_list_pluck($auteur_terms, 'name'));
            }

            return '';
        }
    );
});

?>