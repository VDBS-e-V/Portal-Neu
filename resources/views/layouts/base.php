<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="de">
<head>
	<?php require __DIR__ . '/../parts/head.php'; ?>
</head>
<body class="layout">
	<div class="site-header" role="banner">
		<?php require __DIR__ . '/../parts/header.php'; ?>
	</div>

	<main class="site-main" role="main">
		<div class="container">
			<?= $content ?? '' ?>
		</div>
	</main>

	<div class="site-footer" role="contentinfo">
		<?php require __DIR__ . '/../parts/footer.php'; ?>
	</div>
</body>
</html>