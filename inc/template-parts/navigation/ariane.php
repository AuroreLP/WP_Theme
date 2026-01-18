<div class="ariane">
    <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a> > 
    <?php
    $post_type = get_post_type();
    $categories = get_the_category();
    $genres = get_the_terms(get_the_ID(), 'genre');
    
    // Afficher le type de publication si ce n'est pas un article standard
    if ($post_type === 'chroniques') {
        echo '<a href="' . esc_url(get_permalink(get_page_by_path('liste-chroniques'))) . '">Chroniques</a> > ';
        
        // Afficher la hiérarchie des genres
        if (!empty($genres) && !is_wp_error($genres)) {
            // Trier les genres pour avoir les parents en premier
            $genre_hierarchy = array();
            
            foreach ($genres as $genre) {
                if ($genre->parent == 0) {
                    // C'est un genre parent
                    $genre_hierarchy['parent'] = $genre;
                } else {
                    // C'est un sous-genre
                    $genre_hierarchy['child'] = $genre;
                }
            }
            
            // Afficher le genre parent s'il existe
            if (isset($genre_hierarchy['parent'])) {
                echo '<a href="' . esc_url(get_term_link($genre_hierarchy['parent']->term_id, 'genre')) . '">' . esc_html($genre_hierarchy['parent']->name) . '</a> > ';
            }
            
            // Afficher le sous-genre s'il existe
            if (isset($genre_hierarchy['child'])) {
                echo '<a href="' . esc_url(get_term_link($genre_hierarchy['child']->term_id, 'genre')) . '">' . esc_html($genre_hierarchy['child']->name) . '</a> > ';
            }
        }
        
    } elseif ($post_type !== 'post') {
        $post_type_object = get_post_type_object($post_type);
        if ($post_type_object) {
            echo '<a href="' . esc_url(get_post_type_archive_link($post_type)) . '">' . esc_html($post_type_object->labels->name) . '</a> > ';
        }
    } elseif (!empty($categories)) {
        // Si c'est un article avec une catégorie
        echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '">' . esc_html($categories[0]->name) . '</a> > ';
    }
    ?>
    <?php the_title(); ?>
</div>