<?php get_header(); ?>

<main class="content">
    <div class="container-legal">
        <?php
        while (have_posts()) : the_post();
        ?>
            <div class="container-content">
                <div class="page-header">
                    <h1><?php the_title(); ?></h1>
                </div>
                
                <div class="page-text">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>