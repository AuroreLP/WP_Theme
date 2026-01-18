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
                    $auteurs = get_the_term_list(get_the_ID(), 'auteur', ' – ', ', ');
                    if ($auteurs) {
                        echo wp_kses_post($auteurs);
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
                    <div class="book-info">
                        <p>
                            <?php 
                                $date_pub = get_post_meta(get_the_ID(), 'date_publication', true);
                                if ($date_pub) echo '<strong>Année de publication:</strong> ' . esc_html($date_pub);
                                    
                            ?>
                        </p>
                        <p> 
                            <?php $pages = get_post_meta(get_the_ID(), 'pages', true);
                            if ($pages) echo '<strong>Pages:</strong> ' . esc_html($pages); ?>
                        </p>
                    </div>
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