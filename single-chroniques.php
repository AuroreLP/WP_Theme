<?php get_header(); ?>

<main class="single-chronique">

<?php if (have_posts()) :
while (have_posts()) : the_post(); ?>

<article>

    <?php get_template_part('inc/template-parts/chronique/header'); ?>

    <hr>

    <?php get_template_part('inc/template-parts/chronique/meta'); ?>

    <div class="chronique-content">
        <div class="chronique-text">
            <?php if (has_excerpt()): ?>
                <div class="chronique-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            <?php endif; ?>

            <?php
            // =================
            // SECTIONS EDITORIALES
            // =================

            $resume = get_post_meta(get_the_ID(), 'resume', true);
            $avis = get_post_meta(get_the_ID(), 'avis', true);
            $spoiler = get_post_meta(get_the_ID(), 'spoiler', true);
            $conclusion = get_post_meta(get_the_ID(), 'conclusion', true);

            if ($resume) :
                get_template_part('inc/template-parts/chronique/section', 'resume');
            endif;

            if ($avis) :
                get_template_part('inc/template-parts/chronique/section', 'avis');
            endif;

            if ($spoiler) :
                get_template_part('inc/template-parts/chronique/section', 'spoiler');
            endif;

            if ($conclusion) :
                get_template_part('inc/template-parts/chronique/section', 'conclusion');
            endif;

            // fallback si ancien contenu Gutenberg
            if (!$resume && !$avis && !$spoiler && !$conclusion) :
                the_content();
            endif;
            ?>

        </div>
        <div class="chronique-image">
            
                <?php 
                if (has_post_thumbnail()) {
                     the_post_thumbnail('medium');
                } else {
                    echo '<p>Aucune couverture disponible</p>';
                }
                ?>
                <div>
                    <?php
                        $type_media = get_the_terms(get_the_ID(), 'type_media');
                        $type_media_slug = $type_media && !is_wp_error($type_media) ? $type_media[0]->slug : '';

                        if ($type_media_slug && file_exists(get_template_directory() . "/inc/template-parts/chronique/sidebar-{$type_media_slug}.php")) {
                            get_template_part('inc/template-parts/chronique/sidebar', $type_media_slug);
                        } else {
                            // fallback générique si pas de fichier spécifique
                            get_template_part('inc/template-parts/chronique/sidebar', 'default');
                        }
                    ?>
                </div>
        </div>
    </div>

    <div class="single-date">
        Chronique rédigée le <?php echo esc_html(get_the_date('d/m/Y')); ?>
    </div>

    <?php get_template_part('inc/template-parts/components/related-chroniques'); ?>

    <?php
    if(comments_open() || get_comments_number()){
        comments_template();
    }
    ?>

</article>

<?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>
