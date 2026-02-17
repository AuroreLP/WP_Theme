<h1 class="chronique-title">
    <?php the_title(); ?><span><?php
        $post_id = get_the_ID();

        // 1️⃣ Initialiser Pods + récupérer les artistes liés (relation Pods)
        $pod = pods('chroniques', $post_id);
        $artistes_lies = $pod->field('artistes_lies');

        // 2️⃣ Récupérer le champ texte libre via Pods (pour gérer le répétable)
        $artiste_texte = $pod->field('artistes_texte');

        if (!empty($artistes_lies)) {

            // Normalisation en tableau
            if (!is_array($artistes_lies)) {
                $artistes_lies = array($artistes_lies);
            }

            $artistes_noms = array();

            foreach ($artistes_lies as $artiste) {

                // Pods retourne un tableau avec ID et infos
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

        } elseif (!empty($artiste_texte)) {

            // 🔹 Fallback texte libre - gestion de plusieurs noms
            if (!is_array($artiste_texte)) {
                $artiste_texte = array($artiste_texte);
            }

            $artiste_texte = array_map('trim', $artiste_texte);
            echo ' – ' . esc_html(implode(', ', $artiste_texte));
        }

    ?></span>
</h1>