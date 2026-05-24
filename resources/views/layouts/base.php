<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="de">
<head>
	<?php require __DIR__ . '/../parts/head.php'; ?>
</head>
<body class="layout">
	<?php require __DIR__ . '/../parts/header.php'; ?>

	<main class="site-main" role="main">
		<div class="container">
			<?= $content ?? '' ?>
		</div>
	</main>

	<?php require __DIR__ . '/../parts/footer.php'; ?>
</body>
</html>