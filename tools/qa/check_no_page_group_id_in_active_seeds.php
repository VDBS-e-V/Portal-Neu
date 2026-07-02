<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$seedsDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';

if (!is_dir($seedsDir)) {
    fwrite(STDERR, "database/seeds nicht gefunden.\n");
    exit(1);
}

function split_seed_statements_for_check(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $lineComment = false;
    $blockComment = false;
    $len = strlen($sql);

    for ($i = 0; $i < $len; $i++) {
        $ch = $sql[$i];
        $next = $i + 1 < $len ? $sql[$i + 1] : '';

        if ($lineComment) {
            $buffer .= $ch;
            if ($ch === "\n") {
                $lineComment = false;
            }
            continue;
        }
        if ($blockComment) {
            $buffer .= $ch;
            if ($ch === '*' && $next === '/') {
                $buffer .= $next;
                $i++;
                $blockComment = false;
            }
            continue;
        }
        if ($quote !== null) {
            $buffer .= $ch;
            if ($ch === "\\" && $i + 1 < $len) {
                $buffer .= $sql[++$i];
                continue;
            }
            if ($ch === $quote) {
                if ($next === $quote) {
                    $buffer .= $sql[++$i];
                    continue;
                }
                $quote = null;
            }
            continue;
        }
        if ($ch === '-' && $next === '-') {
            $lineComment = true;
            $buffer .= $ch . $next;
            $i++;
            continue;
        }
        if ($ch === '#') {
            $lineComment = true;
            $buffer .= $ch;
            continue;
        }
        if ($ch === '/' && $next === '*') {
            $blockComment = true;
            $buffer .= $ch . $next;
            $i++;
            continue;
        }
        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $quote = $ch;
            $buffer .= $ch;
            continue;
        }
        if ($ch === ';') {
            $statements[] = $buffer;
            $buffer = '';
            continue;
        }
        $buffer .= $ch;
    }

    if (trim($buffer) !== '') {
        $statements[] = $buffer;
    }

    return $statements;
}

$problems = [];

foreach (glob($seedsDir . DIRECTORY_SEPARATOR . '*.sql') ?: [] as $seedFile) {
    $sql = file_get_contents($seedFile);
    if ($sql === false || stripos($sql, 'page_group_id') === false) {
        continue;
    }

    foreach (split_seed_statements_for_check($sql) as $statement) {
        if (stripos($statement, 'page_group_id') === false) {
            continue;
        }
        if (stripos($statement, 'ids_legacy_deprecations') !== false) {
            continue;
        }
        if (stripos($statement, 'MINI PROJECT 12.1: deaktiviertes Legacy-page_group_id-Statement') !== false) {
            continue;
        }
        if (preg_match('/\b(INSERT|REPLACE|UPDATE|ALTER|DELETE|SELECT)\b[^;]*\bpt_menu_items\b[^;]*\bpage_group_id\b/is', $statement)) {
            $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $seedFile);
            $problems[] = $relative . ': enthält aktiven pt_menu_items.page_group_id-Seed-Code';
        }
    }
}

if ($problems !== []) {
    echo "Aktive Seeds enthalten noch page_group_id-Code, der nach Mini-Projekt 10 fehlschlagen kann:\n";
    foreach ($problems as $problem) {
        echo " - {$problem}\n";
    }
    exit(1);
}

echo "OK: Aktive Seeds enthalten keine gefährlichen pt_menu_items.page_group_id-Verweise mehr.\n";