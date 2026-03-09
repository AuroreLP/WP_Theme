<?php
/**
 * Functions — Theme Bootstrap
 *
 * This is the main entry point for all theme logic. It loads nothing
 * directly — it only includes modular files from the inc/ directory.
 *
 * File loading order matters:
 * 1. Setup files first (theme supports, assets, customizer, security)
 * 2. Content types and taxonomies
 * 3. Feature-specific logic (bilans, ACF)
 * 4. Utility helpers
 *
 * Each included file is self-contained: it registers its own hooks,
 * filters, and functions. This file should never contain function
 * definitions or hook registrations directly.
 *
 * @package turningpages
 */

// ── Theme setup: core supports, asset enqueuing, customizer, security ──
require_once get_template_directory() . '/inc/setup/theme-support.php';
require_once get_template_directory() . '/inc/setup/enqueue.php';
require_once get_template_directory() . '/inc/setup/customizer.php';
require_once get_template_directory() . '/inc/setup/security.php';

// ── Admin features: logo manager, dashboard widgets ──
require_once get_template_directory() . '/inc/functions/logo-manager.php';
require_once get_template_directory() . '/inc/functions/admin-dashboard.php';

// ── Content types: CPTs, taxonomies, search, taxonomy display helpers ──
require_once get_template_directory() . '/inc/functions/post-types.php';
require_once get_template_directory() . '/inc/functions/search-config.php';
require_once get_template_directory() . '/inc/functions/taxonomy-helpers.php';

// ── Bilans: quarterly report stats and ACF field configuration ──
require_once get_template_directory() . '/inc/functions/bilan-stats.php';
require_once get_template_directory() . '/inc/functions/acf-config.php';

// ── Helpers: formatting utilities used across templates ──
require_once get_template_directory() . '/inc/helpers/formatting.php';