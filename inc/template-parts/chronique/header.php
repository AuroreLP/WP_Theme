<h1 class="chronique-title">
    <?php the_title(); ?><span><?php
        $post_id = get_the_ID();

        // 1️⃣ Récupérer les artistes liés (relation Pods)
        $artistes_lies = get_post_meta($post_id, 'artistes_lies', true);

        // 2️⃣ Récupérer le champ texte libre
        $artiste_texte = get_post_meta($post_id, 'artistes_texte', true);

        if (!empty($artistes_lies)) {

            // Normalisation en tableau
            if (is_string($artistes_lies)) {
                if (strpos($artistes_lies, ',') !== false) {
                    $artistes_ids = explode(',', $artistes_lies);
                } else {
                    $artistes_ids = array($artistes_lies);
                }
            } else {
                $artistes_ids = (array) $artistes_lies;
            }

            $artistes_noms = array();

            foreach ($artistes_ids as $artiste_id) {

                $artiste_id = trim($artiste_id);

                if (!empty($artiste_id)) {
                    $artiste_nom = get_the_title($artiste_id);
                    $artiste_url = get_permalink($artiste_id);

                    $artistes_noms[] = '<a href="' . esc_url($artiste_url) . '">' . esc_html($artiste_nom) . '</a>';
                }
            }

            if (!empty($artistes_noms)) {
                echo ' – ' . implode(', ', $artistes_noms);
            }

        } elseif (!empty($artiste_texte)) {

            // 🔹 Fallback texte libre (sans lien)
            echo ' – ' . esc_html($artiste_texte);
        }
    ?></span>
</h1>