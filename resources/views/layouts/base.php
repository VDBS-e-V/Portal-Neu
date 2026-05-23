<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="de">
<head>
	<?php require __DIR__ . '/../parts/head.php'; ?>
</head>
<?php
$sidebarPath = __DIR__ . '/../parts/sidebar.php';
$hasSidebar = file_exists($sidebarPath);
?>
<body class="layout<?= $hasSidebar ? '' : ' layout--no-sidebar' ?>">
	<div class="site-header" role="banner">
		<?php require __DIR__ . '/../parts/header.php'; ?>
	</div>

	<?php if ($hasSidebar): ?>
	<aside class="site-sidebar" role="complementary">
		<div class="container">
			<?php require $sidebarPath; ?>
		</div>
	</aside>
	<?php endif; ?>

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