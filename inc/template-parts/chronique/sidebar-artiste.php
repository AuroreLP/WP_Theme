<?php
/**
 * Template Part — Sidebar: Artiste Profile
 *
 * Displays biographical metadata for an artiste (portrait page):
 * - Birth and death dates
 * - Nationality
 *
 * Dates come from Pods custom fields (date_naissance, date_deces)
 * stored as date strings. Empty or '0000-00-00' values are treated
 * as unset. date_i18n() is used instead of date() to respect the
 * WordPress locale setting for month names.
 *
 * The dash between birth and death dates follows this logic:
 * - Birth only:     "01/01/1900 –" (open-ended, still alive)
 * - Both dates:     "01/01/1900 – 31/12/1980"
 * - Death only:     "31/12/1980" (birth unknown)
 * - Neither:        section hidden entirely
 *
 * Used in: single-artiste.php (via get_template_part)
 *
 * @package turningpages
 */
?>

<div class="book-info">

<?php
// ── Retrieve raw date values from Pods fields ──
$date_naissance_raw = get_post_meta( get_the_ID(), 'date_naissance', true );
$date_deces_raw     = get_post_meta( get_the_ID(), 'date_deces', true );

// ── Format birth date ──
$date_naissance = '';
if ( ! empty( $date_naissance_raw ) && $date_naissance_raw !== '0000-00-00' ) {
    $timestamp = strtotime( $date_naissance_raw );
    if ( $timestamp ) {
        $date_naissance = date_i18n( 'd/m/Y', $timestamp );
    }
}

// ── Format death date ──
$date_deces = '';
if ( ! empty( $date_deces_raw ) && $date_deces_raw !== '0000-00-00' ) {
    $timestamp = strtotime( $date_deces_raw );
    if ( $timestamp ) {
        $date_deces = date_i18n( 'd/m/Y', $timestamp );
    }
}

// ── Nationalities (comma-separated if multiple) ──
$nationalite_terms = get_the_terms( get_the_ID(), 'nationalite' );
$nationalites = ( $nationalite_terms && ! is_wp_error( $nationalite_terms ) )
    ? implode( ', ', wp_list_pluck( $nationalite_terms, 'name' ) )
    : '';
?>

<?php if ( $date_naissance || $date_deces ) : ?>
    <p class="artiste-dates">
        <?php if ( $date_naissance ) : ?>
            <?php echo esc_html( $date_naissance ); ?>

            <?php if ( $date_deces ) : ?>
                – <?php echo esc_html( $date_deces ); ?>
            <?php else : ?>
                –
            <?php endif; ?>

        <?php elseif ( $date_deces ) : ?>
            <?php echo esc_html( $date_deces ); ?>
        <?php endif; ?>
    </p>
<?php endif; ?>

<?php if ( $nationalites ) : ?>
    <p class="artiste-nationalite">
        <strong>Nationalité :</strong>
        <?php echo esc_html( $nationalites ); ?>
    </p>
<?php endif; ?>

</div>