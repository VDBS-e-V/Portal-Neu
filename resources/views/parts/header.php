<?php declare(strict_types=1); ?>
<header class="site-header" role="banner">
	<div class="header-top">
		<div class="header-top-logo" onclick="window.location.href='/'" style="cursor: pointer;">
			<img src="/assets/images/bildmarken/bildmarke_breit.png" alt="Logo">
		</div>
		<div class="header-top-areas">
			<ul class="header-top-areas-list">
				<?php
				$currPath = $path ?? (isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/');
				$isAdmin = str_starts_with((string)$currPath, '/development/web-control');
				if (!empty($headerAreas)):
					foreach ($headerAreas as $ha):
						$slug = (string) ($ha['slug'] ?? '');
						$areaUrl = ($slug === '' || $slug === 'main') ? '/' : ('/' . ltrim($slug, '/'));
						$adminHref = '/development/web-control/areas/select?area_id=' . urlencode((string)($ha['id'] ?? ''));
						$href = $isAdmin ? $adminHref : $areaUrl;
						$selected = isset($current_area['id']) && (string)$current_area['id'] === (string)($ha['id'] ?? '');
				?>
						<li class="header-top-areas-list-item<?= $selected ? ' is-active' : '' ?>">
							<a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="link--no-style"<?= $selected ? ' aria-current="true"' : '' ?>><?= htmlspecialchars((string)($ha['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></a>
						</li>
					<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>
	</div>

	<div class="header-bottom">
		<div class="header-bottom-pagename" onclick="window.location.href='<?php echo htmlspecialchars($areaRootLink ?? '#', ENT_QUOTES, 'UTF-8'); ?>'" style="cursor: pointer;">
			<h2 class="area-name"><?= htmlspecialchars($areaName ?? 'Home', ENT_QUOTES, 'UTF-8') ?></h2>
			<h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Home', ENT_QUOTES, 'UTF-8') ?></h1>
		</div>
		<div class="header-bottom-nav">
			<ul class="header-bottom-nav-list">
				<?php if (!empty($headerMenus)): ?>
					<?php foreach ($headerMenus as $mi): ?>
						<li class="header-bottom-nav-list-item">
							<?php
								$url = (string) ($mi['url'] ?? ('/' . ltrim((string)($mi['slug'] ?? ''), '/')));
								$title = (string) ($mi['title'] ?? ($mi['name'] ?? ''));
							?>
							<a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" class="link--no-style"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
		<div class="header-bottom-search">

		</div>
		<div class="header-bottom-user">
			<div class="header-bottom-user-avatar">
				<img src="/assets/images/avatars/default.jpg" alt="User Avatar">
			</div>

			<div class="header-user-popover">
				<ul class="header-user-popover-list">
					<li class="header-user-popover-list-item">
						<a href="/profile" class="link--no-style">Profile</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/settings" class="link--no-style">Settings</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/logout" class="link--no-style">Logout</a>
					</li>
				</ul>
			</div>

			<div class="header-bottom-user-login">

			</div>
		</div>
	</div>
</header>