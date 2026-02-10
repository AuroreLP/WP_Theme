<?php


// INCLUSION DES FICHIERS
// Configuration du thème
require_once get_template_directory() . '/inc/setup/theme-support.php';
require_once get_template_directory() . '/inc/setup/enqueue.php';
require_once get_template_directory() . '/inc/setup/customizer.php';
require_once get_template_directory() . '/inc/setup/security.php'; 

// Post types et taxonomies
require_once get_template_directory() . '/inc/functions/post-types.php';
require_once get_template_directory() . '/inc/functions/search-config.php';
require_once get_template_directory() . '/inc/functions/taxonomy-helpers.php';

// Bilans
require_once get_template_directory() . '/inc/functions/bilan-stats.php';
require_once get_template_directory() . '/inc/functions/acf-config.php';

// Helpers
require_once get_template_directory() . '/inc/helpers/formatting.php';

?>