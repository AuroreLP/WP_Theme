<?php get_header(); ?>

<main class="single-chronique">
    <?php if(have_posts()):
    while(have_posts()) : the_post();
        
    // Récupérer les catégories et tags
    $categories = get_the_category();
    $tags = get_the_tags();
    ;?>
    <article>
        <h1 class="chronique-title"><?php the_title(); ?></h1>
        <hr>
        <div class="chronique-meta">
                <?php if($categories): ?>
                    <div class="article-category">
                        <?php foreach($categories as $category): ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        <?php endforeach; ?>
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
      
        <div class="article-content">
            <div class="chronique-text">
                <?php if (has_excerpt()): ?>
                    <div class="chronique-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
                <?php the_content() ;?>
            </div>
            <div class="chronique-image">
                <?php if (has_post_thumbnail()): ?>
                    <img src="<?php echo esc_url( get_the_post_thumbnail_url(get_the_ID(), 'medium') ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                <?php endif; ?>
            </div>
        </div>
    <div class="single-date">Article rédigé le <?php echo esc_html( get_the_date('d/m/Y') ); ?></div>
    <?php include('inc/template-parts/components/related-articles.php'); ?>
            <!-- ########################## -->
            <!-- Comments section -->
            <!-- ########################## -->
            <?php
            if(comments_open() || get_comments_number()){
                comments_template();
            }
            ;?>
    </article> 
    <?php
        endwhile;
    endif
    ;?>
</main>
<?php get_footer(); ?>