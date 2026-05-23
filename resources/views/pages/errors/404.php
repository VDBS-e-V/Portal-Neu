<?php declare(strict_types=1); ?>

<h1><?= htmlspecialchars($title ?? '404 – Nicht gefunden', ENT_QUOTES, 'UTF-8') ?></h1>
<p>Die angeforderte Seite existiert nicht.</p>
<ul>
	<li>Path: <code><?= htmlspecialchars($path ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
	<li>Now: <code><?= htmlspecialchars($now ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
</ul>