<?php
/**
 * Template Name: Liste Chroniques
 *
 * Custom page template for the main chroniques listing page.
 * Displays all chroniques with client-side filtering by media type,
 * genre, theme, and nationality.
 *
 * How the filtering works:
 * - ALL chroniques are loaded in a single query (posts_per_page = -1)
 * - Each card stores filter data in data-* attributes
 * - filter-chroniques.js reads these attributes and shows/hides cards
 * - Pagination is also handled client-side (8 posts per page)
 *
 * This means the full dataset must be in the DOM for filters to work.
 * Acceptable for up to ~200-300 chroniques. Beyond that, will consider
 * switching to AJAX-based filtering for better initial load performance.
 *
 * @package turningpages
 */

get_header();
?>

<main class="content">
    <?php if ( get_field( 'chroniques_title_section' ) ) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post( get_field( 'chroniques_title_section' ) ); ?></h1>
        </div>
    <?php endif; ?>

    <div class="container">

        <?php /* ── Filter dropdowns — values read by filter-chroniques.js ── */ ?>
        <div class="filters-wrapper">
            <div class="filters-container">

                <?php /* Media type (livre, film, série, podcast) */ ?>
                <select id="filter-media">
                    <option value="all">Par type de média</option>
                    <?php
                    $type_media = get_terms( array(
                        'taxonomy'   => 'type_media',
                        'parent'     => 0,
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                    if ( ! empty( $type_media ) && ! is_wp_error( $type_media ) ) {
                        foreach ( $type_media as $media ) {
                            echo '<option value="' . esc_attr( $media->slug ) . '">' . esc_html( $media->name ) . '</option>';
                        }
                    }
                    ?>
                </select>

                <?php /* Genre (parent genres only — sub-genres are on the cards) */ ?>
                <select id="filter-genre">
                    <option value="all">Par genre</option>
                    <?php
                    $genres_principaux = get_terms( array(
                        'taxonomy'   => 'genre',
                        'parent'     => 0,
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                    if ( ! empty( $genres_principaux ) && ! is_wp_error( $genres_principaux ) ) {
                        foreach ( $genres_principaux as $genre ) {
                            echo '<option value="' . esc_attr( $genre->slug ) . '">' . esc_html( $genre->name ) . '</option>';
                        }
                    }
                    ?>
                </select>

                <?php /* Themes */ ?>
                <select id="filter-theme">
                    <option value="all">Par thème</option>
                    <?php
                    $themes = get_terms( array(
                        'taxonomy'   => 'theme',
                        'hide_empty' => true,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                    if ( ! empty( $themes ) && ! is_wp_error( $themes ) ) {
                        foreach ( $themes as $theme ) {
                            echo '<option value="' . esc_attr( $theme->slug ) . '">' . esc_html( $theme->name ) . '</option>';
                        }
                    }
                    ?>
                </select>

                <?php /* Nationality */ ?>
                <select id="filter-nation">
                    <option value="all">Par pays</option>
                    <?php
                    $nations = get_terms( array(
                        'taxonomy'   => 'nationalite',
                        'hide_empty' => true,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                    if ( ! empty( $nations ) && ! is_wp_error( $nations ) ) {
                        foreach ( $nations as $nation ) {
                            echo '<option value="' . esc_attr( $nation->slug ) . '">' . esc_html( $nation->name ) . '</option>';
                        }
                    }
                    ?>
                </select>

            </div>
        </div>

        <?php
        /**
         * Chroniques loop — loads ALL published chroniques.
         *
         * posts_per_page = -1 is intentional here: the client-side JS
         * filters need every post in the DOM to show/hide them.
         * Pagination is handled by filter-chroniques.js (8 per page).
         */
        $args = array(
            'post_type'      => 'chroniques',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        $query = new WP_Query( $args );
        ?>

        <div class="posts-grid">
            <?php if ( $query->have_posts() ) : ?>

                <?php while ( $query->have_posts() ) : $query->the_post();

                    /**
                     * Build filter data-attributes for this card.
                     *
                     * Genre: uses the parent genre slug for filtering
                     * (sub-genres fall under their parent in the dropdown).
                     */
                    $genre_info     = tp_get_chronique_genre_display();
                    $term           = $genre_info['term'] ?? null;
                    $genre_principal = ( $term && $term->parent ) ? get_term( $term->parent, 'genre' ) : $term;

                    $chronique_themes = tp_get_chronique_themes();
                    $themes_slugs     = $chronique_themes ? wp_list_pluck( $chronique_themes, 'slug' ) : array();

                    $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                    $nation_slug   = ( $nations_terms && ! is_wp_error( $nations_terms ) ) ? $nations_terms[0]->slug : '';

                    $media_terms = get_the_terms( get_the_ID(), 'type_media' );
                    $media_slug  = ( $media_terms && ! is_wp_error( $media_terms ) ) ? $media_terms[0]->slug : '';

                    get_template_part( 'inc/template-parts/components/cards', 'chronique', array(
                        'genre'  => $genre_principal ? $genre_principal->slug : '',
                        'themes' => implode( ' ', $themes_slugs ),
                        'nation' => $nation_slug,
                        'media'  => $media_slug,
                    ) );

                endwhile; ?>

            <?php else : ?>
                <p>Aucune chronique trouvée</p>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>

    </div>

    <?php /* Pagination container — populated by filter-chroniques.js */ ?>
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>