<?php
require_once __DIR__ . "/partials/image.php";
require_once __DIR__ . "/partials/lqip.php";
?>

<!DOCTYPE html>
<html lang="en-gb">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Developers Against Bloat</title>
		<meta name="description" content="The 'anti-content' website.">
		<script type="module" async src="script.js"></script>
		<link rel="stylesheet" href="style.css">
		<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✨</text></svg>">

		<!-- low quality image previews -->
		<?= getLqipLinkTags(); ?>
	</head>
	<body>
		<header class="max-width">
			<h1>✨ Test Site ✨</h1>
			<dl class="hits">
				<dt>Hits</dt>
				<dd class="number"><?= require(__DIR__ . "/partials/hits.php"); ?></dd>
			</dl>
		</header>
		<main class="max-width">
			<p>Welcome to this test site! If you come back later then things might change.</p>
			<p>Coming soon:</p>
			<ul>
				<li><s>Live hit counter</s> ✅</li>
				<li><s>Low-resolution image placeholders for slow connections.</s> ✅</li>
				<li>Something worth reading and interacting with.</li>
			</ul>
			<p>For now you can have a look at this wonderful image.</p>
			<figure>
    			<?= image("/assets/wht.avif", "black and white portrait of William Howard Taft"); ?>
			<figcaption>William Howard Taft - A large president with a variable file size.</figcaption>
			</figure>
		</main>
		<footer class="max-width">
			<fieldset>
				<legend>Color Scheme</legend>
				<label><input name="color-scheme" type="radio" value="light dark" checked>System</label>
				<label><input name="color-scheme" type="radio" value="light">Light</label>
				<label><input name="color-scheme" type="radio" value="dark">Dark</label>
			</fieldset>

			<h2>Page Load Statistics</h2>
			<dl>
				<dt>Total Asset Size</dt>
				<dd><span id="asset-size">-.-</span></dd>
				<dt>Estimated Download Time on Slow 4G (1.2 Mb/s)</dt>
				<dd><span id="download-time">-.-</span></dd>
			</dl>
			<noscript>This tidbit requires JavaScript to be enabled</noscript>
		</footer>
	</body>
</html>
