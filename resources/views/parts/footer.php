<?php declare(strict_types=1); ?>
<hr>
<footer>
	<small>&copy; <?= date('Y') ?> <?= htmlspecialchars(getenv('APP_NAME') ?: 'App', ENT_QUOTES, 'UTF-8') ?></small>
</footer>