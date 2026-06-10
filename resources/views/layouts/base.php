<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="de">
<head>
	<?php require __DIR__ . '/../parts/head.php'; ?>
</head>
<body class="layout">
	<?php require __DIR__ . '/../parts/header.php'; ?>

	<main role="main">
		<?= $content ?? '' ?>
	</main>

	<?php require __DIR__ . '/../parts/footer.php'; ?>
</body>
</html>