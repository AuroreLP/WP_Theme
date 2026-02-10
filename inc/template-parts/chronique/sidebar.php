<?php
$type = get_the_terms(get_the_ID(), 'type_media');

$type_slug = ($type && !is_wp_error($type))
    ? $type[0]->slug
    : 'default';

get_template_part(
    'inc/template-parts/chronique/sidebar',
    $type_slug
);
?>


<?php
// ===== FILM =====
if ($type_slug === 'film') :

    $duree = get_post_meta(get_the_ID(), 'duree', true);
    $date_sortie = get_post_meta(get_the_ID(), 'date_sortie', true);
?>

    <?php if ($date_sortie): ?>
        <p><strong>Date de sortie :</strong> <?php echo esc_html($date_sortie); ?></p>
    <?php endif; ?>

    <?php if ($duree): ?>
        <p><strong>Durée :</strong> <?php echo esc_html($duree); ?> min</p>
    <?php endif; ?>

<?php endif; ?>

</div>

</div>
