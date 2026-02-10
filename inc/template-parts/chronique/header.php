<h1 class="chronique-title">
    <?php the_title(); ?><span><?php
        // Récupérer les artistes liés via Pods
         $artistes_lies = get_post_meta(get_the_ID(), 'artistes_lies', true);
                    
        if (!empty($artistes_lies)) {
            // Convertir en tableau si c'est une chaîne
            if (is_string($artistes_lies)) {
                // Si c'est plusieurs IDs séparés par des virgules
                if (strpos($artistes_lies, ',') !== false) {
                     $artistes_ids = explode(',', $artistes_lies);
                } else {
                    // Sinon c'est un seul ID
                     $artistes_ids = array($artistes_lies);
                }
             } else {
                $artistes_ids = (array) $artistes_lies;
             }
                        
             // Récupérer les noms des artistes
              if (!empty($artistes_ids)) {
                 $artistes_noms = array();
                foreach ($artistes_ids as $artiste_id) {
                     $artiste_id = trim($artiste_id); // Nettoyer l'ID
                    if (!empty($artiste_id)) {
                         $artiste_nom = get_the_title($artiste_id);
                         $artiste_url = get_permalink($artiste_id);
                         $artistes_noms[] = '<a href="' . esc_url($artiste_url) . '">' . esc_html($artiste_nom) . '</a>';
                     }
                 }
                            
                 if (!empty($artistes_noms)) {
                    echo ' – ' . implode(', ', $artistes_noms);
                }
            }
        }
    ?></span>
</h1>