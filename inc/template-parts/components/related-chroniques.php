<div class="other-single">
    <h3>Autres chroniques</h3>
    <ul class="other-single-container">
        <?php 
        // Création de la requête WP_Query
        $args = array(
            'post_type'      => 'chroniques',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array(get_the_ID()), // Exclut l'article en cours
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
        ?>
            <li class="other-single-box">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium'); ?>
                <?php else : ?>
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/images/default_thumbnail.jpg'); ?>" alt="<?php echo esc_attr('Image de la chronique : ' . get_the_title()); ?>">
                <?php endif; ?>
                <div class="other-box-content">
                    <h5><?php the_title(); ?></h5>
                    <a href="<?php the_permalink(); ?>" class="read-more">Lire la chronique</a>
                </div>
            </li>
        <?php 
            endwhile; 
        endif; 
        wp_reset_postdata();
        ?>
    </ul>
</div>
