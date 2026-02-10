<div class="book-info">

<?php
// ---------- Récupération note ----------
$note = get_post_meta(get_the_ID(), 'note_etoiles', true);
$note = $note !== '' ? floatval($note) : 0;

// On limite entre 0 et 5
$note = max(0, min(5, $note));

if ($note > 0) :

    $note_full  = floor($note);
    $note_half  = ($note - $note_full) >= 0.5 ? 1 : 0;
    $note_empty = 5 - $note_full - $note_half;
?>

    <div class="chronique-rating">

        <?php
        // Étoiles pleines
        for ($i = 0; $i < $note_full; $i++) {
            echo '<ion-icon name="star"></ion-icon>';
        }

        // Demi étoile
        if ($note_half) {
            echo '<ion-icon name="star-half"></ion-icon>';
        }

        // Étoiles vides
        for ($i = 0; $i < $note_empty; $i++) {
            echo '<ion-icon name="star-outline"></ion-icon>';
        }
        ?>

        <span class="rating-value"><?php echo esc_html($note); ?>/5</span>

    </div>

<?php endif; ?>


<?php
// ---------- Autres infos ----------
$date_pub = get_post_meta(get_the_ID(), 'date_publication', true);
$pages = get_post_meta(get_the_ID(), 'pages', true);
?>

<?php if ($date_pub) : ?>
    <p>
        <strong>Année de publication :</strong>
        <?php echo esc_html($date_pub); ?>
    </p>
<?php endif; ?>


<?php if ($pages) : ?>
    <p>
        <strong>Pages :</strong>
        <?php echo esc_html($pages); ?>
    </p>
<?php endif; ?>

</div>
