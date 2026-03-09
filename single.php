<?php
/**
 * Single Template — Standard Blog Post (Article)
 *
 * Displays a full blog article with:
 * - Title
 * - Category links + tag list
 * - Excerpt (if set)
 * - Main content
 * - Sources
 * - Featured image
 * - Publication date
 * - Related articles (same category, with recent fallback)
 * - Comments
 *
 * This template handles all standard 'post' type entries.
 * Posts in the 'bilan' category are redirected to single-bilan.php
 * via the filter in theme-support.php.
 *
 * @package turningpages
 */

get_header();
?>

<main class="single-chronique">

<?php if ( have_posts() ) :
    while ( have_posts() ) : the_post();

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
                    <?php foreach ( $categories as $category ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                            <?php echo esc_html( $category->name ); ?>
                        </a>
                    <?php endforeach; ?>
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

        <?php /* Two-column layout: text content + featured image */ ?>
        <div class="article-content">

            <div class="chronique-text">
                <?php if ( has_excerpt() ) : ?>
                    <div class="chronique-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>

                <?php the_content(); ?>
                <?php get_template_part( 'inc/template-parts/chronique/sources' ); ?>
            </div>

            <div class="chronique-image">
                <?php if ( has_post_thumbnail() ) : ?>
                    <img
                        src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ); ?>"
                        alt="<?php echo esc_attr( get_the_title() ); ?>"
                    >
                <?php endif; ?>
            </div>

        </div>

        <div class="single-date">
            Article rédigé le <?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
        </div>

        <?php
        /**
         * Related articles — same category with recent fallback.
         * Uses get_template_part() instead of include() for child theme
         * compatibility and WordPress hook support.
         */
        get_template_part( 'inc/template-parts/components/related-articles' );
        ?>

        <?php /* Comments section */ ?>
        <?php if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif; ?>

    </article>

    <?php endwhile;
endif; ?>

</main>

<?php get_footer(); ?>