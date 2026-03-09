<?php
/**
 * Footer Template
 *
 * Renders the site footer (social links, copyright, legal pages)
 * and closes the <section class="blog"> wrapper opened in header.php.
 *
 * @package turningpages
 */
?>

    <footer>
        <div>
            <?php /* ── Social links (same component as header) ── */ ?>
            <div class="social">
                <?php get_template_part( 'inc/template-parts/components/social-links' ); ?>
            </div>

            <?php
            /**
             * Copyright line.
             * wp_date() respects the timezone set in Settings > General,
             * unlike PHP's native date().
             */
            ?>
            <p>&copy; <?php echo esc_html( get_bloginfo( 'name' ) ); ?> - <?php echo esc_html( wp_date( 'Y' ) ); ?></p>
        </div>

        <?php
        /**
         * Legal page links.
         *
         * Pages are resolved by slug instead of hardcoded IDs.
         * Slugs are portable across environments (local → staging → production)
         * because they are part of the content, not auto-incremented by the DB.
         *
         * If a page slug is changed in the admin, update it here too.
         * The link is only rendered if the page exists (avoids broken links).
         */
        $legal_pages = array(
            'mentions-legales'            => 'Mentions Légales',
            'politique-de-confidentialite' => 'Politique de confidentialité',
        );
        ?>
        <div class="legal">
            <?php foreach ( $legal_pages as $slug => $label ) :
                $page = get_page_by_path( $slug );
                if ( $page ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </a>
                <?php endif;
            endforeach; ?>
        </div>
    </footer>

<?php /* ── Close main site wrapper opened in header.php ── */ ?>
</section>

<?php
/**
 * wp_footer() — outputs enqueued scripts, plugin assets, and admin bar.
 * Must be present before </body>.
 */
wp_footer();
?>
</body>
</html>