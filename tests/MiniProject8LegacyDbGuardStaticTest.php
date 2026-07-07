<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$files = [
    $root . '/src/Repository/VerwaltungStatsRepository.php',
    $root . '/src/Repository/EntityAuditRepository.php',
    $root . '/resources/views/pages/verwaltung/index.php',
];

$forbidden = [
    'pt_page_groups',
    'pt_permission_group_page_group_access',
    'page_group_id',
];

$errors = [];
foreach ($files as $file) {
    if (!is_file($file)) {
        continue;
    }
    $content = file_get_contents($file) ?: '';
    foreach ($forbidden as $term) {
        if (str_contains($content, $term)) {
            $errors[] = $file . ' enthält noch ' . $term;
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

echo 'OK: MiniProject8LegacyDbGuardStaticTest bestanden.' . PHP_EOL;
