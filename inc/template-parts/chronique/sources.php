<?php
$sources = get_post_meta(get_the_ID(), '_post_sources', true);
if (!empty($sources)) : 
    $lines = array_filter(array_map('trim', explode("\n", $sources)));
?>
    <div class="chronique-sources">
        <h4>Sources</h4>
        <ul class="sources-list">
            <?php foreach ($lines as $line) : ?>
                <li><?php echo wp_kses_post($line); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
