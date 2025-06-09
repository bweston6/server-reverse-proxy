<?php
function getLqipLinkTags(): string {
    $lqips = apcu_entry("lqips", function() {
        $files = scandir(ROOT_DIR . "/assets/.cache/");
        return array_filter($files, function ($file) {
            return str_ends_with($file, "lqip.avif");
        });
    });

    foreach($lqips as $lqip) {
        ?>
		<link rel="preload" as="image" href="/assets/.cache/<?= $lqip ?>" fetchpriority="high">
        <?php
    }
    return ob_get_clean();
} ?>
