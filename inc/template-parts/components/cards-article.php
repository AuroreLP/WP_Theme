<?php
$args = wp_parse_args($args ?? [], [
    'category_slug' => '',
    'category_name' => ''
]);
?>

<article class="post-box articles" data-category="<?php echo esc_attr($args['category_slug']); ?>">

    <!-- Image -->
    <div class="article-img">
        <?php if (has_post_thumbnail()) : ?>
            <img 
                src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" 
                alt="<?php echo esc_attr(get_the_title()); ?>">
        <?php endif; ?>
    </div>

    <!-- Contenu -->
    <div class="article-presentation">

        <?php if ($args['category_name']) : ?>
            <span class="category">
                <?php echo esc_html($args['category_name']); ?>
            </span>
        <?php endif; ?>

        <h2 class="article-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <span class="article-date">
            <?php echo esc_html(get_the_date('d/m/Y')); ?>
        </span>

    </div>

    <!-- Overlay -->
    <div class="article-overlay">
        <div class="overlay-content">

            <p>
                <?php echo wp_trim_words(get_the_excerpt(), 50, '...'); ?>
            </p>

            <a class="article-btn" href="<?php the_permalink(); ?>">
                Lire l'article
                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                    <path class="arrow-path"
                        fill="none"
                        stroke-width="48"
                        stroke-linecap="square"
                        stroke-miterlimit="10"
                        d="M268 112l144 144-144 144M392 256H100"/>
                </svg>
            </a>

        </div>
    </div>

</article>
