<?php
/**
 * Navigation principale
 */
?> 
<nav class="posts-filter">
    <ul>
        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="filter-item <?php echo is_front_page() ? 'is-active' : ''; ?>">Home</a></li>
        <li><a href="<?php echo esc_url(home_url('/liste-chroniques/')); ?>" class="filter-item <?php echo is_page('liste-chroniques') ? 'is-active' : ''; ?>">Chroniques</a></li>
        <li><a href="<?php echo esc_url(home_url('/journal/')); ?>" class="filter-item <?php echo is_page('journal') ? 'is-active' : ''; ?>">Journal</a></li>
        <li><a href="<?php echo esc_url(home_url('/a-propos/')); ?>" class="filter-item <?php echo is_page('a-propos') ? 'is-active' : ''; ?>">À propos</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="filter-item <?php echo is_page('contact') ? 'is-active' : ''; ?>">Contact</a></li>
        <li class="search-desktop"><?php get_search_form(); ?></li>
    </ul>
</nav>
