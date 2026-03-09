<?php
/**
 * Template Name: Liste Artistes
 *
 * Custom page template for the artistes/portraits listing page.
 * Displays all artist profiles with client-side filtering by role
 * and nationality.
 *
 * Same client-side filtering pattern as page-chroniques.php:
 * - All artistes loaded at once (posts_per_page = -1)
 * - filter-artistes.js handles show/hide and pagination
 *
 * The 'artiste' CPT is managed by Pods. The slug is rewritten
 * from /artiste/ to /portrait/ via a filter in post-types.php.
 *
 * @package turningpages
 */

get_header(); ?>

<main class="content">
    <?php if ( get_field( 'artistes_title_section' ) ) : ?>
        <div class="heading">
            <h1><?php echo wp_kses_post( get_field( 'artistes_title_section' ) ); ?></h1>
        </div>
    <?php endif; ?>

    <div class="container">

        <?php /* ── Filter dropdowns — values read by filter-artistes.js ── */ ?>
        <div class="filters-wrapper">
            <div class="filters-container">

                <?php /* Role (auteur·ice, réalisateur·ice, etc.) */ ?>
                <select id="filter-role">
                    <option value="all">Par rôle</option>
                    <?php
                    /**
                     * Role filter uses term NAME (not slug) as the option value.
                     * This is intentional: role names contain the "point médian"
                     * (e.g. "Auteur·ice") which would be stripped from slugs.
                     * The JS filter matches against data-role which also uses the name.
                     */
                    $roles = get_terms( array(
                        'taxonomy'   => 'role',
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ) );
                    if ( ! empty( $roles ) && ! is_wp_error( $roles ) ) {
                        foreach ( $roles as $role_term ) {
                            echo '<option value="' . esc_attr( $role_term->name ) . '">' . esc_html( $role_term->name ) . '</option>';
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
         * Artistes loop — alphabetical by title.
         * All loaded at once for client-side filtering.
         */
        $args_artistes = array(
            'post_type'      => 'artiste',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        );
        $query_artistes = new WP_Query( $args_artistes );
        ?>

        <div class="posts-grid">
            <?php if ( $query_artistes->have_posts() ) : ?>

                <?php while ( $query_artistes->have_posts() ) : $query_artistes->the_post();

                    // Role — uses name (not slug) for point médian compatibility
                    $roles_terms = get_the_terms( get_the_ID(), 'role' );
                    $role_name   = ( ! is_wp_error( $roles_terms ) && ! empty( $roles_terms ) )
                        ? $roles_terms[0]->name
                        : '';

                    // Nationality
                    $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                    $nation_slug   = ( ! is_wp_error( $nations_terms ) && ! empty( $nations_terms ) )
                        ? $nations_terms[0]->slug
                        : '';

                    get_template_part( 'inc/template-parts/components/cards', 'artiste', array(
                        'role'   => $role_name,
                        'nation' => $nation_slug,
                    ) );

                endwhile; ?>

                <?php wp_reset_postdata(); ?>

            <?php else : ?>
                <p>Aucun créateur trouvé</p>
            <?php endif; ?>
        </div>

    </div>

    <?php /* Pagination container — populated by filter-artistes.js */ ?>
    <nav class="nav-pagination">
        <ul class="pagination"></ul>
    </nav>

</main>

<?php get_footer(); ?>