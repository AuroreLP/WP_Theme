<?php
/**
 * Search Form Template
 *
 * Custom search form used in the search overlay (header.php).
 * Loaded via get_search_form() which looks for searchform.php in the theme.
 *
 * The form submits a GET request to the homepage with the 's' parameter,
 * which WordPress intercepts and routes to search.php.
 *
 * The SVG icon is inlined (not via Ionicons) for instant rendering
 * without waiting for the Ionicons library to load.
 *
 * @package turningpages
 */
?>

<form role="search" method="get" class="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input
        type="search"
        name="s"
        id="search"
        value="<?php echo esc_attr( get_search_query() ); ?>"
        placeholder="Que recherchez-vous?"
        aria-label="Rechercher"
        required
    >
    <button type="submit" id="btn-search" aria-label="Rechercher">
        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
            <path d="M464 428L339.92 303.9a160.48 160.48 0 0030.72-94.58C370.64 120.37 298.27 48 209.32 48S48 120.37 48 209.32s72.37 161.32 161.32 161.32a160.48 160.48 0 0094.58-30.72L428 464zM209.32 319.69a110.38 110.38 0 11110.37-110.37 110.5 110.5 0 01-110.37 110.37z"/>
        </svg>
    </button>
</form>