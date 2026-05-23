<?php declare(strict_types=1); ?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/assets/css/app.css">

<?php $appName = $_ENV['APP_NAME'] ?? ''; ?>
<title><?= htmlspecialchars((($title ?? '') !== '') ? ($title . ' - ' . $appName) : $appName, ENT_QUOTES, 'UTF-8') ?></title>