<?php
/**
 * Navigation principale
 */
?> 
<nav class="posts-filter">
    <ul>
        <li><a href="<?php echo esc_url(home_url('/liste-chroniques/')); ?>" class="filter-item <?php echo is_page('liste-chroniques') ? 'is-active' : ''; ?>">Chroniques</a></li>
        <li><a href="<?php echo esc_url(home_url('/journal/')); ?>" class="filter-item <?php echo is_page('journal') ? 'is-active' : ''; ?>">Journal</a></li>
        <!-- <li><a href="<?php echo esc_url(home_url('/portraits/')); ?>" class="filter-item <?php echo is_page('portraits') ? 'is-active' : ''; ?>">Portraits</a></li>-->
        <li><a href="<?php echo esc_url(home_url('/a-propos/')); ?>" class="filter-item <?php echo is_page('a-propos') ? 'is-active' : ''; ?>">À propos</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="filter-item <?php echo is_page('contact') ? 'is-active' : ''; ?>">Contact</a></li>
        <button class="search-trigger" aria-label="Rechercher">
            <ion-icon name="search-outline"></ion-icon>
        </button>
    </ul>
</nav>
