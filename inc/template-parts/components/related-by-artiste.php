<?php
/**
 * Template Part — Related Works by Artiste
 *
 * Displays all chroniques and articles linked to the current artiste
 * via the Pods relational field 'artistes_lies'. Shown on the artiste
 * portrait page as a "Ses œuvres" section.
 *
 * How the relation works:
 * - Each chronique/post can link to one or more artistes via a Pods
 *   relationship field called 'artistes_lies'
 * - This query does a reverse lookup: find all posts where
 *   'artistes_lies' contains the current artiste's ID
 * - The LIKE comparison is used because Pods stores relationship IDs
 *   in a serialized format in post_meta
 *
 * NOTE: meta_query with LIKE on serialized data works but isn't
 * performant at scale. For a literary blog with hundreds of posts,
 * this is fine. For thousands, consider using a Pods relationship
 * table query instead.
 *
 * Used in: single-artiste.php (portrait pages)
 *
 * @package turningpages
 */
?>

<div class="other-single">
    <h3>Ses œuvres</h3>
    <ul class="other-single-container">
        <?php
        $artiste_id = get_the_ID();

        /**
         * Reverse relationship query: find posts linked to this artiste.
         * Searches both chroniques and standard posts.
         */
        $args = array(
            'post_type'      => array( 'chroniques', 'post' ),
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'artistes_lies',
                    'value'   => $artiste_id,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $query_artistes = new WP_Query( $args );

        if ( $query_artistes->have_posts() ) :
            while ( $query_artistes->have_posts() ) : $query_artistes->the_post();

                $roles_terms = get_the_terms( get_the_ID(), 'role' );
                $role_name   = ( ! is_wp_error( $roles_terms ) && ! empty( $roles_terms ) )
                    ? $roles_terms[0]->name
                    : '';

                $nations_terms = get_the_terms( get_the_ID(), 'nationalite' );
                $nation_slug   = ( ! is_wp_error( $nations_terms ) && ! empty( $nations_terms ) )
                    ? $nations_terms[0]->slug
                    : '';

                get_template_part( 'inc/template-parts/components/cards', 'artiste', array(
                    'role'   => $role_name,
                    'nation' => $nation_slug,
                ) );

            endwhile;
            wp_reset_postdata();

        else : ?>
            <p class="aucune-oeuvre">Aucune œuvre chroniquée pour le moment.</p>
        <?php endif; ?>
    </ul>
</div>