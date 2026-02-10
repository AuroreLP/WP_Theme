<?php
$args = wp_parse_args($args ?? [], [
    'genre'  => '',
    'themes' => '',
    'nation' => '',
    'media'  => ''
]);

$genre_info = get_chronique_genre_display();
$genre_name = $genre_info ? $genre_info['name'] : 'Non classé';
?>

<article 
    class="post-box chroniques"
    data-category="chroniques"
    data-genre="<?php echo esc_attr($args['genre']); ?>"
    data-theme="<?php echo esc_attr($args['themes']); ?>"
    data-nation="<?php echo esc_attr($args['nation']); ?>"
    data-media="<?php echo esc_attr($args['media']); ?>"
>

    <!-- Image -->
    <div class="article-img">
        <?php if (has_post_thumbnail()) : ?>
            <img 
                src="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" 
                alt="<?php echo esc_attr(get_the_title()); ?>"
            >
        <?php endif; ?>
    </div>

    <!-- Infos visibles -->
    <div class="article-presentation">

        <span class="category">
            <?php echo esc_html($genre_name); ?>
        </span>

        <h2 class="article-title">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </h2>

        <span class="article-date">
            <?php echo esc_html(get_the_date('d/m/Y')); ?>
        </span>

    </div>

    <!-- Overlay résumé -->
    <div class="article-overlay">
        <div class="overlay-content">

            <p>
                <?php echo wp_trim_words(get_the_excerpt(), 45, '...'); ?>
            </p>

            <a 
                class="article-btn" 
                href="<?php the_permalink(); ?>"
                aria-label="Lire la chronique"
            >
                Lire la chronique

                <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                    <path
                        class="arrow-path"
                        fill="none"
                        stroke-width="48"
                        stroke-linecap="square"
                        stroke-miterlimit="10"
                        d="M268 112l144 144-144 144M392 256H100"
                    />
                </svg>

            </a>

        </div>
    </div>

</article>
