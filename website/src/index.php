<?php
define("ROOT_DIR", __DIR__);
require_once ROOT_DIR . "/partials/lqip.php";
?>

<!DOCTYPE html>
<html lang="en-gb">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Blog | Ben Weston</title>
		<meta name="description" content="The 'anti-content' website.">
		<script type="module" async src="script.js"></script>
		<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✨</text></svg>">
		<!-- <link rel="stylesheet" href="style.css"> -->
		<style>
			<?php require ROOT_DIR . "/style.css" ?>
		</style>

		<!-- low quality image previews -->
		<?= getLqipLinkTags(); ?>
	</head>
	<body>
		<header>
			<section class="top" aria-label="top">
				<div class="wrapper">
					<p class="h1">Ben Weston</p>
					<p>Welcome! Today is <?= date("l F j, Y") ?> and this website has had <?= require(ROOT_DIR . "/partials/hits.php"); ?> hits.</p>
				</div>
			</section>
			<section class="bottom" aria-label="bottom">
				<nav aria-label="primary" class="wrapper">
					<a href="#main">
						<h1>Blog</h1>
						<p>Scroll to enter ↓</p>
					</a>
				</nav>
			</section>
		</header>

		<main id="main" class="wrapper">
			<?php
			$posts = glob(ROOT_DIR . '/blog/*.php');

			foreach ($posts as $post) {
				echo "<article>";
    			require $post;   
				echo "</article>";
			} ?>
		</main>
		<footer>
			<div class="wrapper">
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
			</div>
		</footer>
	</body>
</html>
