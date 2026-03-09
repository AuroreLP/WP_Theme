<?php
/**
 * Template Part — Breadcrumb Navigation (Fil d'Ariane)
 *
 * Renders a simple breadcrumb trail for single posts, chroniques,
 * and other post types. Structure varies by content type:
 *
 *   Chronique: Accueil > Chroniques > Genre Parent > Sous-genre > Title
 *   Article:   Accueil > Catégorie > Title
 *   Artiste:   Accueil > Portraits > Title
 *   Other CPT: Accueil > CPT Archive > Title
 *
 * The chronique breadcrumb displays the full genre hierarchy
 * (parent > child) when both are assigned, giving users a clear
 * path back to genre archives.
 *
 * NOTE: The "Chroniques" link points to the page with slug
 * 'liste-chroniques' (fetched via get_page_by_path). If this slug
 * changes, the breadcrumb link will break.
 *
 * Used in: single-chroniques.php, single.php, single-artiste.php
 *
 * @package turningpages
 */
?>

<div class="ariane">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Accueil</a> >

    <?php
    $post_type  = get_post_type();
    $categories = get_the_category();
    $genres     = get_the_terms( get_the_ID(), 'genre' );

    // ── Chroniques: Chroniques > Genre Parent > Sous-genre ──
    if ( 'chroniques' === $post_type ) {

        $chroniques_page = get_page_by_path( 'liste-chroniques' );
        if ( $chroniques_page ) {
            echo '<a href="' . esc_url( get_permalink( $chroniques_page ) ) . '">Chroniques</a> > ';
        }

        /**
         * Genre hierarchy: show parent first, then child.
         * Mirrors the logic in tp_get_chronique_genre_display()
         * but outputs both levels for the breadcrumb trail.
         */
        if ( ! empty( $genres ) && ! is_wp_error( $genres ) ) {
            $genre_hierarchy = array();

            foreach ( $genres as $genre ) {
                if ( $genre->parent == 0 ) {
                    $genre_hierarchy['parent'] = $genre;
                } else {
                    $genre_hierarchy['child'] = $genre;
                }
            }

            if ( isset( $genre_hierarchy['parent'] ) ) {
                echo '<a href="' . esc_url( get_term_link( $genre_hierarchy['parent']->term_id, 'genre' ) ) . '">'
                     . esc_html( $genre_hierarchy['parent']->name ) . '</a> > ';
            }

            if ( isset( $genre_hierarchy['child'] ) ) {
                echo '<a href="' . esc_url( get_term_link( $genre_hierarchy['child']->term_id, 'genre' ) ) . '">'
                     . esc_html( $genre_hierarchy['child']->name ) . '</a> > ';
            }
        }

    // ── Other CPTs (artiste, etc.) — link to archive ──
    } elseif ( 'post' !== $post_type ) {

        $post_type_object = get_post_type_object( $post_type );
        if ( $post_type_object ) {
            $archive_link = get_post_type_archive_link( $post_type );
            if ( $archive_link ) {
                echo '<a href="' . esc_url( $archive_link ) . '">' . esc_html( $post_type_object->labels->name ) . '</a> > ';
            }
        }

    // ── Standard post — show first category ──
    } elseif ( ! empty( $categories ) ) {

        echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">'
             . esc_html( $categories[0]->name ) . '</a> > ';
    }
    ?>

    <?php the_title(); ?>
</div>