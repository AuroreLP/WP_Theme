<h1 class="chronique-title">
    <?php the_title(); ?><span><?php
        $post_id = get_the_ID();

        // 1. Récupérer les artistes liés (relation Pods)
        $pod = pods('chroniques', $post_id);
        $artistes_lies = $pod->field('artistes_lies');

        if (!empty($artistes_lies)) {

            // Normalisation en tableau
            if (!is_array($artistes_lies)) {
                $artistes_lies = array($artistes_lies);
            }

            $artistes_noms = array();

            foreach ($artistes_lies as $artiste) {
                if (is_array($artiste)) {
                    $artiste_id = $artiste['ID'];
                } else {
                    $artiste_id = trim($artiste);
                }

                if (!empty($artiste_id)) {
                    $artiste_nom = get_the_title($artiste_id);
                    $artiste_url = get_permalink($artiste_id);
                    $artistes_noms[] = '<a href="' . esc_url($artiste_url) . '">' . esc_html($artiste_nom) . '</a>';
                }
            }

            if (!empty($artistes_noms)) {
                echo ' – ' . implode(', ', $artistes_noms);
            }

        } else {

            // Fallback : taxonomie auteur (sans lien)
            $auteur_terms = get_the_terms($post_id, 'auteur');
            if (!empty($auteur_terms) && !is_wp_error($auteur_terms)) {
                $auteur_noms = array_map(function($term) {
                    return esc_html($term->name);
                }, $auteur_terms);
                echo ' – ' . implode(', ', $auteur_noms);
            }
        }

    ?></span>
</h1>