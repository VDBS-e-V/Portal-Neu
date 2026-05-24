<?php declare(strict_types=1); ?>
<header class="site-header" role="banner">
	<div class="header-top">
		<div class="header-top-logo" onclick="window.location.href='/'" style="cursor: pointer;">
			<img src="/assets/images/bildmarken/bildmarke_breit.png" alt="Logo">
		</div>
		<div class="header-top-areas">
			<ul class="header-top-areas-list">
				<?php
					foreach ($areaNav ?? [] as $area) {
						$href = htmlspecialchars($area['href'] ?? '#', ENT_QUOTES, 'UTF-8');
						$label = htmlspecialchars($area['label'] ?? '', ENT_QUOTES, 'UTF-8');
						$aClasses = 'link--no-style';
						$liClasses = 'header-top-areas-list-item';
						$aria = '';
						if (!empty($area['active'])) {
							$aClasses .= ' is-active';
							$aria = ' aria-current="page"';
						}

						echo '<li class="' . $liClasses . '">';
						echo '<a href="' . $href . '" class="' . $aClasses . '"' . $aria . '>';
						if (!empty($area['icon'])) {
							echo '<img src="' . htmlspecialchars($area['icon'], ENT_QUOTES, 'UTF-8') . '" alt="" class="header-area-icon" /> ';
						}
						echo $label;
						echo '</a>';
						echo '</li>';
					}
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
				<?php
				$renderNav = function(array $items) use (&$renderNav) {
					foreach ($items as $navItem) {
						$href = htmlspecialchars($navItem['href'] ?? '#', ENT_QUOTES, 'UTF-8');
						$label = htmlspecialchars($navItem['label'] ?? '', ENT_QUOTES, 'UTF-8');
						$aClasses = 'link--no-style';
						$liClasses = 'header-bottom-nav-list-item';
						$aria = '';
						if (!empty($navItem['active'])) {
							$aClasses .= ' is-active';
							$aria = ' aria-current="page"';
						}

						echo '<li class="' . $liClasses . '">';
						echo '<a href="' . $href . '" class="' . $aClasses . '"' . $aria . '>' . $label . '</a>';
						if (!empty($navItem['children'])) {
							echo '<ul class="header-bottom-nav-sublist">';
							$renderNav($navItem['children']);
							echo '</ul>';
						}
						echo '</li>';
					}
				};

				$renderNav($headerNav ?? []);
				?>
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