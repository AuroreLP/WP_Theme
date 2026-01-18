// ####################
// Navigation
// ####################

jQuery(document).ready(function($){
    // Menu mobile toggle
    const menu_toggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.posts-filter ul');

    if(menu_toggle && menu){
        menu_toggle.addEventListener('click', () => {
            menu_toggle.classList.toggle('is-active');
            menu.classList.toggle('is-active');
        });
    }
})
