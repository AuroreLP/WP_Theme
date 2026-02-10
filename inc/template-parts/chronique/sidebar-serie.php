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
    $date_sortie = get_post_meta(get_the_ID(), 'date_sortie', true);
    $saisons = get_post_meta(get_the_ID(), 'saisons', true);
    $duree_episode = (int) get_post_meta(get_the_ID(), 'duree_episode', true);
    ?>

    <?php if ($date_sortie): ?>
        <p>
            <strong>Année de sortie :</strong>
            <?php echo esc_html($date_sortie); ?>
        </p>
    <?php endif; ?>

    <?php if ($saisons): ?>

        <p>
            <strong>Saisons :</strong>
            <?php echo esc_html($saisons); ?>
        </p>

    <?php endif; ?>

    <?php if ($duree_episode > 0): ?>

        <p>
            <strong>Durée/épisode :</strong>
            <?php echo esc_html(format_duree($duree_episode)); ?>
        </p>

    <?php endif; ?>

</div>


