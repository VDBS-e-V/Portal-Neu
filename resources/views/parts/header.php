<?php declare(strict_types=1); ?>
<header class="site-header" role="banner">
	<div class="header-top">
		<div class="header-top-logo" onclick="window.location.href='/'" style="cursor: pointer;">
			<img src="/assets/images/bildmarken/bildmarke_breit.png" alt="Logo">
		</div>
		<div class="header-top-areas">
			<ul class="header-top-areas-list">
                <li class="header-top-areas-list-item">
                    <a href="/" class="link--no-style">Start</a>
                </li>
                <li class="header-top-areas-list-item">
                    <a href="/dev" class="link--no-style">Development</a>
                </li>
                <li class="header-top-areas-list-item">
                    <a href="/styleguide" class="link--no-style">Style Guide</a>
                </li>
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
                <li class="header-bottom-nav-list-item">
                    <a href="/ueber-das-portal" class="link--no-style">Über das Portal</a>
                </li>
                <li class="header-bottom-nav-list-item">
                    <a href="/zugang-zum-portal" class="link--no-style">Zugang zum Portal</a>
                </li>
                <li class="header-bottom-nav-list-item">
                    <a href="/faq" class="link--no-style">FAQ</a>
                </li>
                <li class="header-bottom-nav-list-item">
                    <a href="/kontakt" class="link--no-style">Kontakt</a>
                </li>
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