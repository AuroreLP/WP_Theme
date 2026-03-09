<?php
/**
 * Single Template — Bilan Trimestriel (Quarterly Report)
 *
 * Displays a quarterly reading report with:
 * - Title, categories, tags
 * - Excerpt + featured image
 * - Aggregate stats (books read, pages, listening hours)
 * - Charts (genres, author gender parity, nationalities) via Chart.js
 * - Per-month breakdown with coups de coeur highlights
 * - Free-form conclusions (Gutenberg content)
 * - Related articles + comments
 *
 * Data flow:
 * 1. ACF fields (mois_1, mois_2, mois_3) define the quarter's months
 * 2. get_trimestre_stats() computes all stats from those months
 * 3. Stats are displayed in PHP templates (stat cards, month grids)
 * 4. Chart data is passed to JS via wp_localize_script in enqueue.php
 * 5. bilan-charts.js renders the Chart.js canvases
 *
 * This template is loaded via the single_template filter in
 * theme-support.php when a post belongs to the 'bilan' category.
 *
 * PAUSED until I figure out the real need of this template
 *
 * @package turningpages
 */

get_header();
?>

<main class="single-chronique">
    <?php if ( have_posts() ) :
        while ( have_posts() ) : the_post();

            // Retrieve the three month slugs from ACF
            $mois_1 = get_field( 'mois_1' );
            $mois_2 = get_field( 'mois_2' );
            $mois_3 = get_field( 'mois_3' );
            $mois_trimestre = array_filter( array( $mois_1, $mois_2, $mois_3 ) );

            // Guard: bail early if no months are configured
            if ( empty( $mois_trimestre ) ) {
                echo '<p class="error">' . esc_html( '⚠️ Aucun mois défini pour ce bilan. Veuillez configurer les champs ACF.' ) . '</p>';
                get_footer();
                return;
            }

            // Compute all stats for the quarter
            $stats      = get_trimestre_stats( $mois_trimestre );
            $categories = get_the_category();
            $tags       = get_the_tags();
    ?>

    <article>

        <h1 class="chronique-title"><?php the_title(); ?></h1>

        <hr>

        <?php /* Category and tag metadata */ ?>
        <div class="chronique-meta">
            <?php if ( $categories ) : ?>
                <div class="article-category">
                    <ul>
                        <?php foreach ( $categories as $category ) : ?>
                            <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                                <?php echo esc_html( $category->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( $tags ) : ?>
                <div class="article-tags">
                    <ul>
                        <?php foreach ( $tags as $tag ) : ?>
                            <li>
                                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
                                    <?php echo esc_html( $tag->name ); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="bilan-content">

            <?php /* Intro section: excerpt + featured image */ ?>
            <section class="intro">
                <?php if ( has_excerpt() ) : ?>
                    <div class="chronique-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>

                <div class="chronique-image">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>"
                             alt="<?php echo esc_attr( get_the_title() ); ?>">
                    <?php endif; ?>
                </div>
            </section>

            <section class="chronique-text">

                <?php /* ── Aggregate quarter stats ── */ ?>
                <div class="bilan-trimestriel">
                    <h2>Mes stats du trimestre</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📚</div>
                            <div class="stat-number"><?php echo esc_html( $stats['total']['livres'] ); ?></div>
                            <div class="stat-label">Livres lus</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📄</div>
                            <div class="stat-number"><?php echo esc_html( number_format( $stats['total']['pages'], 0, ',', ' ' ) ); ?></div>
                            <div class="stat-label">Pages lues</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🎧</div>
                            <div class="stat-number"><?php echo esc_html( number_format( $stats['total']['heures'], 1, ',', ' ' ) ); ?>h</div>
                            <div class="stat-label">Heures écoutées</div>
                        </div>
                    </div>

                    <?php
                    /**
                     * Chart.js canvases.
                     *
                     * These <canvas> elements are populated by bilan-charts.js
                     * using data passed via wp_localize_script in enqueue.php.
                     * Only rendered if the corresponding data exists.
                     */
                    ?>
                    <div class="bilan-graphiques">
                        <div class="graphiques-grid">
                            <?php if ( ! empty( $stats['genres'] ) ) : ?>
                                <div class="graphique-card">
                                    <h3>Genres littéraires</h3>
                                    <canvas id="chart-genres" width="300" height="300"></canvas>
                                </div>
                            <?php endif; ?>

                            <?php if ( $stats['auteurs_total'] > 0 ) : ?>
                                <div class="graphique-card">
                                    <h3>Parité des auteurs</h3>
                                    <canvas id="chart-parite" width="300" height="300"></canvas>
                                </div>
                            <?php endif; ?>

                            <?php if ( ! empty( $stats['nationalites'] ) ) : ?>
                                <div class="graphique-card">
                                    <h3>Littérature de nationalité</h3>
                                    <canvas id="chart-nationalites" width="300" height="300"></canvas>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php /* ── Per-month breakdown ── */ ?>
                <div class="bilan-par-mois">
                    <h2>Et par mois, ça donne quoi?</h2>
                    <div class="mois-grid">
                        <?php
                        $noms_mois = array(
                            '01' => 'Janvier',   '02' => 'Février',  '03' => 'Mars',
                            '04' => 'Avril',     '05' => 'Mai',      '06' => 'Juin',
                            '07' => 'Juillet',   '08' => 'Août',     '09' => 'Septembre',
                            '10' => 'Octobre',   '11' => 'Novembre', '12' => 'Décembre',
                        );

                        foreach ( $stats['par_mois'] as $mois_slug => $mois_data ) :
                            $mois_num = substr( $mois_slug, -2 );
                            $mois_nom = $noms_mois[ $mois_num ] ?? $mois_slug;
                        ?>
                            <div class="mois-card">
                                <h3><?php echo esc_html( $mois_nom ); ?></h3>
                                <ul>
                                    <li><strong><?php echo esc_html( $mois_data['livres'] ); ?></strong> livres</li>
                                    <li><strong><?php echo esc_html( number_format( $mois_data['pages'], 0, ',', ' ' ) ); ?></strong> pages</li>

                                    <?php if ( $mois_data['heures'] > 0 ) : ?>
                                        <li><strong><?php echo esc_html( number_format( $mois_data['heures'], 1, ',', ' ' ) ); ?>h</strong> écoutées</li>
                                    <?php endif; ?>

                                    <?php /* Coups de coeur for this month */ ?>
                                    <?php if ( ! empty( $stats['coups_de_coeur'][ $mois_slug ] ) ) :
                                        foreach ( $stats['coups_de_coeur'][ $mois_slug ] as $livre ) : ?>
                                            <li class="coup-de-coeur-item">
                                                <ion-icon class="heart" name="heart" aria-hidden="true"></ion-icon>
                                                <a href="<?php echo esc_url( $livre['permalink'] ); ?>">
                                                    <?php echo esc_html( $livre['titre'] ); ?>
                                                </a>
                                            </li>
                                        <?php endforeach;
                                    endif; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php /* ── Free-form conclusions (Gutenberg content) ── */ ?>
                <div class="bilan-contenu">
                    <h2>Conclusions</h2>
                    <div class="bilan-refexions">
                        <?php the_content(); ?>
                    </div>
                </div>

            </section>
        </div>

        <div class="single-date">
            Article rédigé le <?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
        </div>

        <?php get_template_part( 'inc/template-parts/components/related-articles' ); ?>

        <?php /* Comments section */ ?>
        <?php if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif; ?>

    </article>

    <?php endwhile;
    endif; ?>

</main>

<?php get_footer(); ?>