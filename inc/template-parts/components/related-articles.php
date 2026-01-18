<div class="other-single">
    <h3>Autres articles</h3>
    <ul class="other-single-container">
        <?php 
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => array(get_the_ID()),
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
                    <div class="no-thumbnail">
                        <img src="<?php echo esc_url('https://via.placeholder.com/300x200?text=Article'); ?>" alt="<?php echo esc_attr('Image de l\'article : ' . get_the_title()); ?>">
                    </div>
                <?php endif; ?>
                <div class="other-box-content">
                    <h5><?php the_title(); ?></h5>
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="read-more">Lire l'article</a>
                </div>
            </li>
        <?php 
            endwhile;
        endif; 
        wp_reset_postdata();
        ?>
    </ul>
</div>