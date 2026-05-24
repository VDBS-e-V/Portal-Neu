<?php declare(strict_types=1); ?>

<div class="error-page">
	<div class="error-panel">
		<div class="error-code">404</div>
		<div class="error-body">
			<h1 class="error-title"><?= htmlspecialchars($title ?? '404 – Nicht gefunden', ENT_QUOTES, 'UTF-8') ?></h1>
			<p class="error-subtitle">Die angeforderte Seite existiert nicht oder wurde verschoben.</p>

			<div class="error-meta">
				<ul>
					<li>Path: <code><?= htmlspecialchars($path ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
					<li>Now: <code><?= htmlspecialchars($now ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
				</ul>
			</div>

			<div class="error-actions">
				<a class="btn btn-primary" href="/" title="Zur Startseite">
					<svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
					Zur Startseite
				</a>
				<a class="btn btn-secondary" href="/kontakt" title="Kontakt / Support">
					<svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 6.5v11a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-11a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1zm1 0l8 5 8-5"/></svg>
					Kontakt / Support
				</a>
			</div>
		</div>
	</div>
</div>