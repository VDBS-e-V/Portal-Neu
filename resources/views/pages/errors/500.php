<?php declare(strict_types=1); ?>

<h1><?= htmlspecialchars($title ?? '500 – Serverfehler', ENT_QUOTES, 'UTF-8') ?></h1>
<p>Es ist ein Fehler aufgetreten.</p>
<ul>
	<li>Path: <code><?= htmlspecialchars($path ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
	<li>Now: <code><?= htmlspecialchars($now ?? '', ENT_QUOTES, 'UTF-8') ?></code></li>
</ul>