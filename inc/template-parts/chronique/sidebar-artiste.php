<div class="book-info">

<?php

// ---------- Récupération données ----------
$date_naissance_raw = get_post_meta(get_the_ID(), 'date_naissance', true);
$date_deces_raw = get_post_meta(get_the_ID(), 'date_deces', true);


// ---------- Formatage dates ----------

// Naissance
$date_naissance = '';
if (!empty($date_naissance_raw) && $date_naissance_raw !== '0000-00-00') {

    $timestamp = strtotime($date_naissance_raw);

    if ($timestamp) {
        $date_naissance = date_i18n('d/m/Y', $timestamp);
    }
}


// Décès
$date_deces = '';
if (!empty($date_deces_raw) && $date_deces_raw !== '0000-00-00') {

    $timestamp = strtotime($date_deces_raw);

    if ($timestamp) {
        $date_deces = date_i18n('d/m/Y', $timestamp);
    }
}


// ---------- Nationalités ----------
$nationalite_terms = get_the_terms(get_the_ID(), 'nationalite');

$nationalites = ($nationalite_terms && !is_wp_error($nationalite_terms))
    ? implode(', ', wp_list_pluck($nationalite_terms, 'name'))
    : '';

?>

<?php if ($date_naissance || $date_deces) : ?>
    <p class="artiste-dates">

        <?php if ($date_naissance) : ?>
            <?php echo esc_html($date_naissance); ?>

            <?php if ($date_deces) : ?>
                – <?php echo esc_html($date_deces); ?>
            <?php else : ?>
                –
            <?php endif; ?>

        <?php elseif ($date_deces) : ?>
            <?php echo esc_html($date_deces); ?>
        <?php endif; ?>

    </p>
<?php endif; ?>


<?php if ($nationalites) : ?>
    <p class="artiste-nationalite">
        <strong>Nationalité :</strong>
        <?php echo esc_html($nationalites); ?>
    </p>
<?php endif; ?>

</div>
