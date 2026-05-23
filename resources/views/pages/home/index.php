<?php declare(strict_types=1); ?>

<h1><?= htmlspecialchars($title ?? 'Home', ENT_QUOTES, 'UTF-8') ?></h1>
<p>OK – Seite rendert.</p>
<ul>
	<li>Path: <code><?= htmlspecialchars($path ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
	<li>Now: <code><?= htmlspecialchars($now ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
</ul>