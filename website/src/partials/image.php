<?php
function image(string $src, string $alt): string {
    $imageCacheUrl = "/assets/.cache/";
    $imageCachePath = dirname(__DIR__) . $imageCacheUrl;

    $url = pathinfo($src);

    $path = dirname(__DIR__) . $src;
    [$width, $height] = getimagesize($path);

    $images = [
        "quarter" => [
            "width" => floor($width / 4),
            "url" => $imageCacheUrl . $url['filename'] . "-" . floor($width / 4) . "." . $url['extension'],
        ],
        "third" => [
            "width" => floor($width / 3),
            "url" => $imageCacheUrl . $url['filename'] . "-" . floor($width / 3) . "." . $url['extension'],
        ],
        "half" => [
            "width" => floor($width / 2),
            "url" => $imageCacheUrl . $url['filename'] . "-" . floor($width / 2) . "." . $url['extension'],
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
                apcu_entry(dirname(__DIR__) . $image['url'], function($file) {
                    return file_exists($file);
                })
        ) {
            continue;
        }
        imageavif(
            imagescale(imagecreatefromavif($path), $image['width']),
            dirname(__DIR__) . $image['url'],
        );
        apcu_delete(dirname(__DIR__) . $image['url']);
    }

    $lqipUrl = $imageCacheUrl . $url['filename'] . "-lqip." . $url['extension'];
    if (!apcu_entry(dirname(__DIR__) . $lqipUrl, function($file) {
        return file_exists($file);
    })

    ) {
        $lqipPath = dirname(__DIR__) . $lqipUrl;
        imageavif(
            imagecreatefromavif($path),
            $lqipPath,
            0,
            10
        );
        apcu_delete(dirname(__DIR__) . $lqipUrl);
        apcu_delete("lqips");
    }

    ob_start();
    ?>

    <img
	    style="aspect-ratio: <?= $width ?> / <?= $height ?>; background-image: url(<?= $lqipUrl ?>)"
        src="<?= $images[array_key_first($images)]['url'] ?>" alt="<?= $alt ?>" 
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
