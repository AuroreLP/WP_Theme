<?php
$args = wp_parse_args($args ?? [], [
    'role'   => '',
    'nation' => '',
]);
?>

<article class="post-box artiste"
         data-role="<?php echo esc_attr($args['role']); ?>"
         data-nation="<?php echo esc_attr($args['nation']); ?>">

    <!-- Image -->
    <div class="article-img">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
        <?php endif; ?>
    </div>

    <!-- Infos -->
    <div class="article-presentation">
        <?php if ($args['role']) : ?>
            <span class="category"><?php echo wp_kses_post($args['role']); ?></span>
        <?php endif; ?>
        <h2 class="article-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <?php if ($args['nation']) : ?>
            <span class="article-nation"><?php echo esc_html($args['nation']); ?></span>
        <?php endif; ?>
    </div>

    <!-- Overlay -->
    <div class="article-overlay">
        <div class="overlay-content">
            <p><?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?></p>
            <a class="article-btn" href="<?php the_permalink(); ?>" aria-label="Lire le portrait">
                Lire le portrait
            </a>
        </div>
    </div>

</article>
