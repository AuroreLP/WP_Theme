<?php
/**
 * 404 Error Page Template
 *
 * Displayed when WordPress cannot find the requested URL.
 * Common causes: deleted posts, changed slugs, typos in URLs,
 * or crawlers hitting non-existent paths.
 *
 * Provides a friendly message, a search form to help users find
 * what they're looking for, and a link back to the homepage.
 *
 * @package turningpages
 */

get_header();
?>

<main class="content">
    <div class="container-legal">
        <div class="container-content">

            <div class="page-header">
                <h1>Page introuvable</h1>
            </div>

            <div class="page-text">
                <p>
                    La page que vous recherchez n'existe pas ou a été déplacée.
                    Vous pouvez utiliser la recherche ci-dessous ou revenir à
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">l'accueil</a>.
                </p>

                <?php get_search_form(); ?>
            </div>

        </div>
    </div>
</main>

<?php get_footer(); ?>