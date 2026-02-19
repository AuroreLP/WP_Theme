<?php
$spoiler = get_post_meta(get_the_ID(), '_chroniques_spoiler', true);

if (!empty($spoiler)) : ?>
    <h2>Avis avec SPOILER</h2>
    <details class="wp-block-details">
      <summary>Clique ici pour te faire spoiler</summary>
      <div class="spoiler-content">
        <?php echo wp_kses_post($spoiler); ?>
      </div>
    </details>
<?php endif; ?>
