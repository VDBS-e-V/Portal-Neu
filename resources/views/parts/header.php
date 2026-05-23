<?php declare(strict_types=1); ?>
<header>
	<div class="header-top">
		<div class="header-top-logo">
			<img src="/assets/images/bildmarken/bildmarke_breit.png" alt="Logo">
		</div>
		<div class="header-top-areas">
			<ul class="header-top-areas-list">
				<li class="header-top-areas-list-item">
					<a href="/areas/area1">Area 1</a>
				</li>
				<li class="header-top-areas-list-item active">
					<a href="/areas/area2">Area 2</a>
				</li>
				<li class="header-top-areas-list-item">
					<a href="/areas/area3">Area 3</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="header-bottom">
		<div class="header-bottom-pagename">
			<h2 class="area-name"><?= htmlspecialchars($areaName ?? 'Home', ENT_QUOTES, 'UTF-8') ?></h2>
			<h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Home', ENT_QUOTES, 'UTF-8') ?></h1>
		</div>
		<div class="header-bottom-nav">
			<ul class="header-bottom-nav-list">
				<li class="header-bottom-nav-list-item">
					<a href="/ueber-das-portal">Über das Portal</a>
				</li>
				<li class="header-bottom-nav-list-item active">
					<a href="/zugang-zum-portal">Zugang zum Portal</a>
				</li>
				<li class="header-bottom-nav-list-item">
					<a href="/faq">FAQ</a>
				</li>
				<li class="header-bottom-nav-list-item">
					<a href="/kontakt">Kontakt</a>
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
						<a href="/profile">Profile</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/settings">Settings</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/logout">Logout</a>
					</li>
				</ul>
			</div>

			<div class="header-bottom-user-login">

			</div>
		</div>
	</div>
</header>