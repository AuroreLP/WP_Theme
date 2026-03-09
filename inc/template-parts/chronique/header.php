<?php
/**
 * Template Part — Chronique Header (Title + Author)
 *
 * Displays the chronique title followed by the author name(s).
 * Author resolution uses a two-step strategy:
 *
 * 1. PRIMARY: Pods relational field 'artistes_lies' — if the chronique
 *    is linked to one or more artiste profiles, their names are displayed
 *    as clickable links to their portrait pages.
 *
 * 2. FALLBACK: 'auteur' taxonomy — if no Pods relation exists, the author
 *    name is pulled from the taxonomy (plain text, no link). This covers
 *    chroniques created before the Pods relation was set up, or cases
 *    where no artiste profile exists yet.
 *
 * Used in: single-chroniques.php (via get_template_part)
 *
 * @package turningpages
 */
?>

<h1 class="chronique-title">
    <?php the_title(); ?><span><?php
        $post_id = get_the_ID();

        /**
         * Step 1: Try Pods relational field.
         *
         * pods() initializes a Pods object for this chronique.
         * ->field('artistes_lies') returns the related artiste post(s).
         * The return format varies (single array vs array of arrays)
         * depending on Pods configuration, so we normalize to an array.
         */
        $pod = pods( 'chroniques', $post_id );
        $artistes_lies = $pod->field( 'artistes_lies' );

        if ( ! empty( $artistes_lies ) ) {

            // Normalize to array (Pods may return a single item or an array)
            if ( ! is_array( $artistes_lies ) ) {
                $artistes_lies = array( $artistes_lies );
            }

            $artistes_noms = array();

            foreach ( $artistes_lies as $artiste ) {
                // Pods returns either an associative array with 'ID' key
                // or a raw ID depending on the relationship format setting
                if ( is_array( $artiste ) ) {
                    $artiste_id = $artiste['ID'];
                } else {
                    $artiste_id = trim( $artiste );
                }

                if ( ! empty( $artiste_id ) ) {
                    $artiste_nom = get_the_title( $artiste_id );
                    $artiste_url = get_permalink( $artiste_id );
                    $artistes_noms[] = '<a href="' . esc_url( $artiste_url ) . '">' . esc_html( $artiste_nom ) . '</a>';
                }
            }

            if ( ! empty( $artistes_noms ) ) {
                echo ' – ' . implode( ', ', $artistes_noms );
            }

        } else {

            /**
             * Step 2: Fallback to 'auteur' taxonomy.
             * Plain text names without links (no archive page for this taxonomy
             * would be meaningful without the artiste profile context).
             */
            $auteur_terms = get_the_terms( $post_id, 'auteur' );
            if ( ! empty( $auteur_terms ) && ! is_wp_error( $auteur_terms ) ) {
                $auteur_noms = array_map( function ( $term ) {
                    return esc_html( $term->name );
                }, $auteur_terms );
                echo ' – ' . implode( ', ', $auteur_noms );
            }
        }

    ?></span>
</h1>