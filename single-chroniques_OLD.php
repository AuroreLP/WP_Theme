<?php
get_header();
?> 

    <main class="single-chronique">
        <?php 
        // Démarrer la boucle WordPress
        if (have_posts()) :
            while (have_posts()) : the_post();
        ?>
        <article>
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
            <hr>
            <div class="chronique-meta">
                <div class="article-tags">
                    <ul>
                        <?php 
                            $nationalites = get_the_terms(get_the_ID(), 'nationalite');
                            if ($nationalites && !is_wp_error($nationalites)) {
                                foreach ($nationalites as $nationalite) {
                                    echo '<li><a href="' . esc_url(get_term_link($nationalite)) . '">littérature ' . esc_html($nationalite->name) . '</a></li>';
                                }
                            }
                        ?>

                            <?php display_chronique_genres_list(); ?>
                            <?php display_chronique_themes_list(); ?>
                    </ul>
                </div>
            </div>
            
            
            <div class="chronique-content">
                <div class="chronique-text">
                    <?php if (has_excerpt()): ?>
                        <div class="chronique-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>

                    <?php the_content(); ?>
                </div>
                <div class="chronique-image">
                    <?php
                    // Affichage des étoiles
                    $note = get_post_meta(get_the_ID(), 'note_etoiles', true);
                    if ($note) {
                        $note_full = floor($note); // Étoiles pleines
                        $note_half = ($note - $note_full) >= 0.5 ? 1 : 0; // Demi-étoile
                        $note_empty = 5 - $note_full - $note_half; // Étoiles vides
                        ?>
                        <div class="chronique-rating">
                            <?php
                            // Étoiles pleines
                            for ($i = 0; $i < $note_full; $i++) {
                                echo '<ion-icon name="star"></ion-icon>';
                            }
                            // Demi-étoile
                            if ($note_half) {
                                echo '<ion-icon name="star-half"></ion-icon>';
                            }
                            // Étoiles vides
                            for ($i = 0; $i < $note_empty; $i++) {
                                echo '<ion-icon name="star-outline"></ion-icon>';
                            }
                            ?>
                            <span class="rating-value"><?php echo esc_html($note); ?>/5</span>
                        </div>
                        <?php
                    }
                    ?>
                    <?php 
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('medium');
                    } else {
                        echo '<p>Aucune couverture disponible</p>';
                    }
                    ?>
                    <?php get_template_part('inc/template-parts/chronique/sidebar'); ?>
                </div>
            </div>
            <div class="single-date">Chronique rédigée le <?php echo esc_html( get_the_date('d/m/Y') ); ?></div>
            <?php include('inc/template-parts/components/related-chroniques.php'); ?>
            <!-- ########################## -->
            <!-- Comments section -->
            <!-- ########################## -->
            <?php
            if(comments_open() || get_comments_number()){
                comments_template();
            }
            ;?>
        </article>
        <?php 
            endwhile;
        endif;
        ?>
    </main>

<?php get_footer(); ?>