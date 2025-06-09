<?php
function image(string $src, string $alt, string $class=""): string {
    $imageCacheUrl = "/assets/.cache/";
    $imageCachePath = ROOT_DIR . $imageCacheUrl;

    $url = pathinfo($src);

    $path = ROOT_DIR . $src;
    [$width, $height] = getimagesize($path);
    $max_width = min($width, 2400);

    $images = [
        "quarter" => [
            "width" => floor($max_width / 4),
            "url" => $imageCacheUrl . $url['filename'] . "-" . floor($max_width / 4) . "." . $url['extension'],
        ],
        "third" => [
            "width" => floor($max_width / 3),
            "url" => $imageCacheUrl . $url['filename'] . "-" . floor($max_width / 3) . "." . $url['extension'],
        ],
        "half" => [
            "width" => floor($max_width / 2),
            "url" => $imageCacheUrl . $url['filename'] . "-" . floor($max_width / 2) . "." . $url['extension'],
        ],
        "full" => [
            "width" => $max_width,
            "url" => $imageCacheUrl . $url['filename'] . "-" . $max_width . "." . $url['extension'],
        ],
        "original" => [
            "width" => $width,
            "url" => $src,
            "noresize" => true,
        ],
    ];

    foreach($images as $image) {
        if (
            array_key_exists('noresize', $image) && $image['noresize'] ||
                apcu_entry(ROOT_DIR . $image['url'], function($file) {
                    return file_exists($file);
                })
        ) {
            continue;
        }
        imageavif(
            imagescale(imagecreatefromavif($path), $image['width']),
            ROOT_DIR . $image['url'],
        );
        apcu_delete(ROOT_DIR . $image['url']);
    }

    $lqipUrl = $imageCacheUrl . $url['filename'] . "-lqip." . $url['extension'];
    if (!apcu_entry(ROOT_DIR . $lqipUrl, function($file) {
        return file_exists($file);
    })

    ) {
        $lqipPath = ROOT_DIR . $lqipUrl;
        imageavif(
            imagescale(
                imagecreatefromavif($path),
                $max_width
            ),
            $lqipPath,
            0,
        );
        apcu_delete(ROOT_DIR . $lqipUrl);
        apcu_delete("lqips");
    }

    ob_start();
    ?>

    <img
	    style="aspect-ratio: <?= $width ?> / <?= $height ?>; background-image: url(<?= $lqipUrl ?>)"
        <?= $class ? "class='$class'" : "" ?>
        src="<?= $images[array_key_last($images)]['url'] ?>" alt="<?= $alt ?>" 
	    srcset="
        <?php foreach($images as $image) {
        echo "{$image['url']} {$image['width']}w,";
        }?>
        " 
	    sizes="
        <?php foreach($images as $image) {
        echo "(width <= {$image['width']}px) {$image['width']}px,";
        }?>
        "
    />

    <?php
    return ob_get_clean();
}
?>
