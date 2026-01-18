<form role="search" method="get" class="search" action="<?php echo esc_url(home_url('/')); ?>">
  <input type="search" 
         name="s" 
         id="search" 
         value="<?php echo esc_attr(get_search_query()); ?>" 
         placeholder="Que recherchez-vous?" 
         aria-label="Rechercher"
         required>
  <button type="submit" id="btn-search" aria-label="Rechercher">
    <ion-icon name="search-outline"></ion-icon>
  </button>
</form> 
