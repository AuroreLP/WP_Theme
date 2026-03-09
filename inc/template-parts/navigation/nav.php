<?php
/**
 * Template Part — Primary Navigation
 *
 * Renders the main site navigation menu with active state highlighting.
 * Displayed in the site header (loaded via get_template_part in header.php).
 *
 * NOTE: This is a hardcoded navigation — links point to fixed page slugs
 * rather than using wp_nav_menu(). This is simpler and avoids the overhead
 * of a registered menu, but means:
 * - Adding/removing items requires editing this file
 * - Changing a page slug will break the corresponding link
 * - Active state is detected via is_page() with the slug
 *
 * The "Portraits" link is commented out for now (page not yet public).
 *
 * The search trigger button opens the search overlay (handled by
 * search-overlay.js). It's inside the <ul> for layout purposes but
 * is semantically a button, not a navigation link.
 *
 * Alternative approach: register a menu location in theme-support.php
 * and use wp_nav_menu() here. This would make the menu editable from
 * Appearance > Menus without touching code.
 *
 * @package turningpages
 */
?>

<nav class="posts-filter">
    <ul>
        <li>
            <a href="<?php echo esc_url( home_url( '/liste-chroniques/' ) ); ?>"
               class="filter-item <?php echo is_page( 'liste-chroniques' ) ? 'is-active' : ''; ?>">
                Chroniques
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"
               class="filter-item <?php echo is_page( 'journal' ) ? 'is-active' : ''; ?>">
                Journal
            </a>
        </li>
        <!-- <li>
            <a href="<?php echo esc_url( home_url( '/portraits/' ) ); ?>"
               class="filter-item <?php echo is_page( 'portraits' ) ? 'is-active' : ''; ?>">
                Portraits
            </a>
        </li> -->
        <li>
            <a href="<?php echo esc_url( home_url( '/a-propos/' ) ); ?>"
               class="filter-item <?php echo is_page( 'a-propos' ) ? 'is-active' : ''; ?>">
                À propos
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
               class="filter-item <?php echo is_page( 'contact' ) ? 'is-active' : ''; ?>">
                Contact
            </a>
        </li>

        <?php /* Search button — opens the overlay defined in header.php */ ?>
        <button class="search-trigger" aria-label="Rechercher">
            <ion-icon name="search-outline" aria-hidden="true"></ion-icon>
        </button>
    </ul>
</nav>