<?php get_header(); ?>

<main class="single-chronique">
    <?php if (have_posts()) :
        while (have_posts()) : the_post();

    // Récupérer les mois depuis ACF
    $mois_1 = get_field('mois_1');
    $mois_2 = get_field('mois_2');
    $mois_3 = get_field('mois_3');

    // Construire le tableau des mois (en filtrant les valeurs vides)
    $mois_trimestre = array_filter(array($mois_1, $mois_2, $mois_3));

    // Si aucun mois n'est défini, afficher un message d'erreur
    if (empty($mois_trimestre)) {
        echo '<p class="error">' . esc_html('⚠️ Aucun mois défini pour ce bilan. Veuillez configurer les champs ACF.') . '</p>';
        get_footer();
        return;
    }

    // Calculer les stats
    $stats = get_trimestre_stats($mois_trimestre);
    $categories = get_the_category();
    $tags = get_the_tags();
    ?>

    <article>
        <h1 class="chronique-title"><?php the_title(); ?></h1>
        <hr>
        <div class="chronique-meta">
            <?php if($categories): ?>
                <div class="article-category">
                    <ul>
                        <?php foreach($categories as $category): ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if($tags): ?>
                <div class="article-tags">
                    <ul>
                        <?php foreach($tags as $tag): ?>
                            <li><a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>"><?php echo esc_html($tag->name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>


        <div class="bilan-content">
            <section class="intro">
                    <?php if (has_excerpt()): ?>
                        <div class="chronique-excerpt">
                    <?php the_excerpt(); ?>
                    <?php endif; ?>
                </div>
                <div class="chronique-image">
                    <?php if (has_post_thumbnail()): ?>
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    <?php endif; ?>
                </div>
            </section>
            <section class="chronique-text">
                <!-- Statistiques globales du trimestre -->
                <div class="bilan-trimestriel">
                    <h2>Mes stats du trimestre</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📚</div>
                            <div class="stat-number"><?php echo esc_html($stats['total']['livres']); ?></div>
                            <div class="stat-label">Livres lus</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📄</div>
                            <div class="stat-number"><?php echo esc_html(number_format($stats['total']['pages'], 0, ',', ' ')); ?></div>
                            <div class="stat-label">Pages lues</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🎧</div>
                            <div class="stat-number"><?php echo esc_html(number_format($stats['total']['heures'], 1, ',', ' ')); ?>h</div>
                            <div class="stat-label">Heures écoutées</div>
                        </div>
                    </div>
                    <!-- Graphiques -->
                    <div class="bilan-graphiques">
                        <div class="graphiques-grid">

                            <!-- Genres -->
                            <?php if (!empty($stats['genres'])): ?>
                            <div class="graphique-card">
                                <h3>Genres littéraires</h3>
                                <canvas id="chart-genres" width="300" height="300"></canvas>
                            </div>
                            <?php endif; ?>

                            <!-- Parité auteurs -->
                            <?php if ($stats['auteurs_total'] > 0): ?>
                            <div class="graphique-card">
                                <h3>Parité des auteurs</h3>
                                <canvas id="chart-parite" width="300" height="300"></canvas>
                            </div>
                            <?php endif; ?>

                            <!-- Nationalités -->
                            <?php if (!empty($stats['nationalites'])): ?>
                            <div class="graphique-card">
                                <h3>Littérature de nationalité</h3>
                                <canvas id="chart-nationalites" width="300" height="300"></canvas>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Stats par mois -->
                <div class="bilan-par-mois">
                    <h2>Et par mois, ça donne quoi?</h2>
                    <div class="mois-grid">
                        <?php 
                        $noms_mois = array(
                            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
                            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
                            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
                            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre'
                        );
                        
                        foreach ($stats['par_mois'] as $mois_slug => $mois_data): 
                            $mois_num = substr($mois_slug, -2);
                            $mois_nom = $noms_mois[$mois_num];
                        ?>
                        
                        <div class="mois-card">
                            <h3><?php echo esc_html($mois_nom); ?></h3>
                            <ul>
                                <li><strong><?php echo esc_html($mois_data['livres']); ?></strong> livres</li>
                                <li><strong><?php echo esc_html(number_format($mois_data['pages'], 0, ',', ' ')); ?></strong> pages</li>
                                <?php if ($mois_data['heures'] > 0): ?>
                                <li><strong><?php echo esc_html(number_format($mois_data['heures'], 1, ',', ' ')); ?>h</strong> écoutées</li>
                                <?php endif; ?>
                                
                                <?php 
                                // Afficher les coups de cœur de ce mois
                                if (!empty($stats['coups_de_coeur'][$mois_slug])): 
                                    foreach ($stats['coups_de_coeur'][$mois_slug] as $livre): 
                                ?>
                                <li class="coup-de-coeur-item">
                                    <ion-icon class="heart" name="heart"></ion-icon> <a href="<?php echo esc_url($livre['permalink']); ?>" target="_blank">
                                        <?php echo esc_html($livre['titre']); ?>
                                    </a>
                                </li>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </ul>
                        </div>
                    
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Contenu libre du bilan -->
                <div class="bilan-contenu">
                    <h2>Conclusions</h2>
                    <div class="bilan-refexions">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>   
        </div>
        <div class="single-date">Article rédigé le <?php echo esc_html(get_the_date('d/m/Y')); ?></div>
        <?php include('inc/template-parts/components/related-articles.php'); ?>
        <!-- ########################## -->
        <!-- Comments section -->
        <!-- ########################## -->
        <?php
            if(comments_open() || get_comments_number()){
                comments_template();
            };
        // =============================================
        // PASSER LES DONNÉES AU JAVASCRIPT
        // =============================================
        if (!empty($stats)) :
        ?>
            <?php endif; ?>
    </article>
    <?php
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>