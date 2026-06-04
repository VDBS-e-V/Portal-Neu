<?php declare(strict_types=1); ?>
<header class="site-header" role="banner">
	<div class="header-top">
		<div class="header-top-logo" onclick="window.location.href='/'" style="cursor: pointer;">
			<img src="/assets/images/bildmarken/bildmarke_breit.png" alt="Logo">
		</div>
		<div class="header-top-areas">
			<ul class="header-top-areas-list">
				<?php if (!empty($headerAreas)): ?>
					<?php foreach ($headerAreas as $ha): ?>
						<?php
							$startPath = (string) ($ha['start_path'] ?? '/');
							$areaUrl = $startPath !== '' ? $startPath : '/';
						?>
						<li class="header-top-areas-list-item">
							<a href="<?= htmlspecialchars($areaUrl, ENT_QUOTES, 'UTF-8') ?>" class="link--no-style"><?= htmlspecialchars((string)($ha['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
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
			<button class="header-bottom-user-avatar" popovertarget="user-popover">
				<img src="/assets/images/avatars/default.jpg" alt="User Avatar">
			</button>

			<div class="header-user-popover" id="user-popover" popover>
				<div class="popover-header">
					<div class="popover-avatar">
						<img src="/assets/images/avatars/default.jpg" alt="Avatar">
					</div>
					<div class="popover-user">
						<div class="popover-name">Jan Brand</div>
						<div class="popover-username">
							<svg class="vdb-icon vdb-icon--sm vdb-icon--medium vdb-icon--current" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-profile"></use></svg>
							<span class="popover-username-text">jan.brand</span>
						</div>
					</div>
				</div>
				<hr class="popover-sep">
				<ul class="header-user-popover-list">
					<li class="header-user-popover-list-item">
						<a href="/profile" class="link--no-style">
							<span class="popover-icon">
								<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-user"></use></svg>
							</span>
							<span>Mein Profil</span>
						</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/settings" class="link--no-style">
							<span class="popover-icon">
								<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-settings"></use></svg>
							</span>
							<span>Kontoeinstellungen</span>
						</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/tickets" class="link--no-style">
							<span class="popover-icon">
								<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-inbox"></use></svg>
							</span>
							<span>Meine Tickets</span>
						</a>
					</li>
				</ul>
				<hr class="popover-sep">
				<ul class="header-user-popover-list">
					<li class="header-user-popover-list-item">
						<a href="/contact" class="link--no-style">
							<span class="popover-icon">
								<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-mail"></use></svg>
							</span>
							<span>Kontakt</span>
						</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/faq" class="link--no-style">
							<span class="popover-icon">
								<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-book"></use></svg>
							</span>
							<span>FAQ</span>
						</a>
					</li>
					<li class="header-user-popover-list-item">
						<a href="/help" class="link--no-style">
							<span class="popover-icon">
								<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-help"></use></svg>
							</span>
							<span>Hilfe</span>
						</a>
					</li>
				</ul>
				<hr class="popover-sep">
				<a href="/logout" class="header-user-popover-logout link--no-style">
					<span class="popover-icon">
						<svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true"><use href="/assets/icons/vdb-icons.svg#icon-logout"></use></svg>
					</span>
					<span>Abmelden</span>
				</a>
			</div>

			<script>
			// Simple popover toggle using existing attributes
			(function(){
				function closeAll() {
					document.querySelectorAll('.header-user-popover[open]').forEach(function(el){ el.removeAttribute('open'); });
				}

				document.addEventListener('click', function(e){
					var btn = e.target.closest('[popovertarget]');
					if (btn) {
						var id = btn.getAttribute('popovertarget');
						var pop = document.getElementById(id);
						if (pop) {
							var isOpen = pop.hasAttribute('open');
							if (isOpen) { pop.removeAttribute('open'); }
							else { closeAll(); pop.setAttribute('open', ''); }
						}
						return;
					}
					// click outside: close
					if (!e.target.closest('.header-user-popover')) { closeAll(); }
				});

				document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeAll(); });
			})();
			</script>

			<div class="header-bottom-user-login">

			</div>
		</div>
	</div>
</header>