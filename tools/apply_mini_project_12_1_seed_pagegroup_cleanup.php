<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$tag = 'mini-project-12-1-' . date('Ymd-His');

function out(string $message): void
{
    echo $message . PHP_EOL;
}

function fail(string $message): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit(1);
}

function normalize_identifier(string $identifier): string
{
    $identifier = trim($identifier);
    $identifier = trim($identifier, " `\t\n\r\0\x0B");
    $identifier = preg_replace('/\s+/', '', $identifier) ?? $identifier;
    return strtolower($identifier);
}

function backup_file(string $path, string $tag): void
{
    if (!is_file($path)) {
        return;
    }

    $backup = $path . '.bak-' . $tag;
    if (!copy($path, $backup)) {
        fail('Backup fehlgeschlagen: ' . $path);
    }
    out('Backup: ' . $backup);
}

function split_top_level_commas(string $text): array
{
    $parts = [];
    $buffer = '';
    $depth = 0;
    $quote = null;
    $len = strlen($text);

    for ($i = 0; $i < $len; $i++) {
        $ch = $text[$i];
        $next = $i + 1 < $len ? $text[$i + 1] : '';

        if ($quote !== null) {
            $buffer .= $ch;
            if ($ch === "\\" && $i + 1 < $len) {
                $buffer .= $text[++$i];
                continue;
            }
            if ($ch === $quote) {
                if ($next === $quote) {
                    $buffer .= $text[++$i];
                    continue;
                }
                $quote = null;
            }
            continue;
        }

        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $quote = $ch;
            $buffer .= $ch;
            continue;
        }

        if ($ch === '(') {
            $depth++;
            $buffer .= $ch;
            continue;
        }

        if ($ch === ')') {
            $depth = max(0, $depth - 1);
            $buffer .= $ch;
            continue;
        }

        if ($ch === ',' && $depth === 0) {
            $parts[] = trim($buffer);
            $buffer = '';
            continue;
        }

        $buffer .= $ch;
    }

    if (trim($buffer) !== '') {
        $parts[] = trim($buffer);
    }

    return $parts;
}

function find_matching_paren(string $text, int $openPos): ?int
{
    $depth = 0;
    $quote = null;
    $len = strlen($text);

    for ($i = $openPos; $i < $len; $i++) {
        $ch = $text[$i];
        $next = $i + 1 < $len ? $text[$i + 1] : '';

        if ($quote !== null) {
            if ($ch === "\\" && $i + 1 < $len) {
                $i++;
                continue;
            }
            if ($ch === $quote) {
                if ($next === $quote) {
                    $i++;
                    continue;
                }
                $quote = null;
            }
            continue;
        }

        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $quote = $ch;
            continue;
        }

        if ($ch === '(') {
            $depth++;
            continue;
        }

        if ($ch === ')') {
            $depth--;
            if ($depth === 0) {
                return $i;
            }
        }
    }

    return null;
}

function strip_legacy_page_group_assignment_suffix(string $suffix): string
{
    if (!preg_match('/\bON\s+DUPLICATE\s+KEY\s+UPDATE\b/i', $suffix, $m, PREG_OFFSET_CAPTURE)) {
        return $suffix;
    }

    $prefix = substr($suffix, 0, $m[0][1] + strlen($m[0][0]));
    $assignments = trim(substr($suffix, $m[0][1] + strlen($m[0][0])));
    $parts = split_top_level_commas($assignments);
    $kept = [];

    foreach ($parts as $part) {
        if (stripos($part, 'page_group_id') !== false) {
            continue;
        }
        $kept[] = $part;
    }

    if ($kept === []) {
        return '';
    }

    return rtrim($prefix) . ' ' . implode(', ', $kept);
}

function find_keyword_outside_strings(string $text, string $keyword): ?int
{
    $upperKeyword = strtoupper($keyword);
    $len = strlen($text);
    $quote = null;

    for ($i = 0; $i < $len; $i++) {
        $ch = $text[$i];
        $next = $i + 1 < $len ? $text[$i + 1] : '';

        if ($quote !== null) {
            if ($ch === "\\" && $i + 1 < $len) {
                $i++;
                continue;
            }
            if ($ch === $quote) {
                if ($next === $quote) {
                    $i++;
                    continue;
                }
                $quote = null;
            }
            continue;
        }

        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $quote = $ch;
            continue;
        }

        if (strtoupper(substr($text, $i, strlen($keyword))) === $upperKeyword) {
            $before = $i === 0 ? ' ' : $text[$i - 1];
            $after = $i + strlen($keyword) >= $len ? ' ' : $text[$i + strlen($keyword)];
            if (!preg_match('/[A-Za-z0-9_]/', $before) && !preg_match('/[A-Za-z0-9_]/', $after)) {
                return $i;
            }
        }
    }

    return null;
}

function remove_page_group_id_from_pt_menu_items_insert(string $statement): ?string
{
    if (!preg_match('/\bINSERT\s+(?:IGNORE\s+)?INTO\s+`?pt_menu_items`?\s*\(/i', $statement, $m, PREG_OFFSET_CAPTURE)) {
        return null;
    }

    $openColumns = strpos($statement, '(', $m[0][1]);
    if ($openColumns === false) {
        return null;
    }
    $closeColumns = find_matching_paren($statement, $openColumns);
    if ($closeColumns === null) {
        return null;
    }

    $columnsText = substr($statement, $openColumns + 1, $closeColumns - $openColumns - 1);
    $columns = split_top_level_commas($columnsText);
    $removeIndexes = [];

    foreach ($columns as $index => $column) {
        if (normalize_identifier($column) === 'page_group_id') {
            $removeIndexes[] = $index;
        }
    }

    if ($removeIndexes === []) {
        return $statement;
    }

    $removeIndexMap = array_flip($removeIndexes);
    $keptColumns = [];
    foreach ($columns as $index => $column) {
        if (!isset($removeIndexMap[$index])) {
            $keptColumns[] = $column;
        }
    }

    $afterColumns = substr($statement, $closeColumns + 1);
    $valuesPosRelative = find_keyword_outside_strings($afterColumns, 'VALUES');
    if ($valuesPosRelative === null) {
        return null;
    }

    $beforeValues = substr($afterColumns, 0, $valuesPosRelative);
    $afterValuesKeyword = substr($afterColumns, $valuesPosRelative + strlen('VALUES'));
    $onDuplicatePos = find_keyword_outside_strings($afterValuesKeyword, 'ON DUPLICATE KEY UPDATE');

    if ($onDuplicatePos === null) {
        $valuesText = trim($afterValuesKeyword);
        $suffix = '';
    } else {
        $valuesText = trim(substr($afterValuesKeyword, 0, $onDuplicatePos));
        $suffix = substr($afterValuesKeyword, $onDuplicatePos);
    }

    $tuples = [];
    $cursor = 0;
    $valuesLen = strlen($valuesText);

    while ($cursor < $valuesLen) {
        while ($cursor < $valuesLen && (ctype_space($valuesText[$cursor]) || $valuesText[$cursor] === ',')) {
            $cursor++;
        }
        if ($cursor >= $valuesLen) {
            break;
        }
        if ($valuesText[$cursor] !== '(') {
            return null;
        }
        $tupleClose = find_matching_paren($valuesText, $cursor);
        if ($tupleClose === null) {
            return null;
        }
        $tupleText = substr($valuesText, $cursor + 1, $tupleClose - $cursor - 1);
        $values = split_top_level_commas($tupleText);
        $keptValues = [];
        foreach ($values as $index => $value) {
            if (!isset($removeIndexMap[$index])) {
                $keptValues[] = $value;
            }
        }
        $tuples[] = '(' . implode(', ', $keptValues) . ')';
        $cursor = $tupleClose + 1;
    }

    if ($tuples === []) {
        return null;
    }

    $prefix = substr($statement, 0, $openColumns + 1);
    $cleanSuffix = strip_legacy_page_group_assignment_suffix($suffix);

    return rtrim($prefix) . implode(', ', $keptColumns) . ')' . $beforeValues . ' VALUES ' . implode(', ', $tuples) . ($cleanSuffix !== '' ? ' ' . trim($cleanSuffix) : '');
}

function statement_is_safe_marker(string $statement): bool
{
    return stripos($statement, 'ids_legacy_deprecations') !== false
        || stripos($statement, 'information_schema') !== false;
}

function patch_sql_statement(string $statement, array &$notes): string
{
    if (stripos($statement, 'page_group_id') === false) {
        return $statement;
    }

    $patchedInsert = remove_page_group_id_from_pt_menu_items_insert($statement);
    if ($patchedInsert !== null) {
        $notes[] = 'pt_menu_items-INSERT von page_group_id befreit';
        return $patchedInsert;
    }

    if (statement_is_safe_marker($statement)) {
        return $statement;
    }

    if (preg_match('/\b(UPDATE|ALTER|DELETE|SELECT|INSERT|REPLACE)\b[^;]*\bpt_menu_items\b[^;]*page_group_id/is', $statement)) {
        $notes[] = 'Legacy-page_group_id-Statement deaktiviert';
        return "/* MINI PROJECT 12.1: deaktiviertes Legacy-page_group_id-Statement\n" . trim($statement) . "\n*/";
    }

    return $statement;
}

function split_sql_statements_preserve_comments(string $sql): array
{
    $statements = [];
    $buffer = '';
    $quote = null;
    $len = strlen($sql);
    $lineComment = false;
    $blockComment = false;

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
            $statements[] = [$buffer, ';'];
            $buffer = '';
            continue;
        }

        $buffer .= $ch;
    }

    if ($buffer !== '') {
        $statements[] = [$buffer, ''];
    }

    return $statements;
}

function patch_sql_file(string $path, string $tag): bool
{
    $sql = file_get_contents($path);
    if ($sql === false) {
        fail('Datei kann nicht gelesen werden: ' . $path);
    }

    if (stripos($sql, 'page_group_id') === false) {
        return false;
    }

    $statements = split_sql_statements_preserve_comments($sql);
    $changed = false;
    $notes = [];
    $new = '';

    foreach ($statements as [$statement, $terminator]) {
        $patched = patch_sql_statement($statement, $notes);
        if ($patched !== $statement) {
            $changed = true;
        }
        $new .= $patched . $terminator;
    }

    if (!$changed) {
        return false;
    }

    backup_file($path, $tag);
    $header = "-- Mini-Projekt 12.1: page_group_id aus aktiven Seeds bereinigt am " . date('c') . "\n";
    if (strpos($new, 'Mini-Projekt 12.1') === false) {
        $new = $header . $new;
    }

    if (file_put_contents($path, $new) === false) {
        fail('Datei kann nicht geschrieben werden: ' . $path);
    }

    out('Aktualisiert: ' . $path);
    foreach (array_unique($notes) as $note) {
        out('  - ' . $note);
    }

    return true;
}

function write_file_with_backup(string $path, string $content, string $tag): void
{
    if (is_file($path)) {
        backup_file($path, $tag);
    }
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fail('Verzeichnis kann nicht erstellt werden: ' . $dir);
    }
    if (file_put_contents($path, $content) === false) {
        fail('Datei kann nicht geschrieben werden: ' . $path);
    }
    out('Aktualisiert: ' . $path);
}

$seedsDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds';
if (!is_dir($seedsDir)) {
    fail('database/seeds nicht gefunden: ' . $seedsDir);
}

$patchedCount = 0;
foreach (glob($seedsDir . DIRECTORY_SEPARATOR . '*.sql') ?: [] as $seedFile) {
    if (patch_sql_file($seedFile, $tag)) {
        $patchedCount++;
    }
}

$qaCheck = <<<'PHPQA'
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
PHPQA;

$runChecks = <<<'PHPRUN'
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = PHP_BINARY;

$commands = [
    ['-l', $root . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php'],
    [$root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_no_page_group_id_in_active_seeds.php'],
    [$root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_integrity_checks.php'],
];

$failed = false;

foreach ($commands as $args) {
    $cmd = array_merge([$php], $args);
    $display = implode(' ', array_map(static fn(string $part): string => '"' . $part . '"', $cmd));
    echo "Running: {$display}\n";
    passthru($display, $exitCode);
    if ($exitCode !== 0) {
        echo "FAILED: {$display}\n";
        $failed = true;
    }
}

if ($failed) {
    echo "Mini-Projekt-12.1-Seed-Cleanup-Checks haben Fehler gefunden.\n";
    exit(1);
}

echo "OK: Mini-Projekt-12.1-Seed-Cleanup-Checks bestanden.\n";
PHPRUN;

write_file_with_backup($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'check_no_page_group_id_in_active_seeds.php', $qaCheck, $tag);
write_file_with_backup($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'qa' . DIRECTORY_SEPARATOR . 'run_identity_seed_cleanup_checks.php', $runChecks, $tag);

out('');
out('Mini-Projekt 12.1 Seed-page_group_id-Cleanup wurde angewendet.');
out('Gepatchte Seed-Dateien: ' . $patchedCount);
out('Bitte ausführen:');
out('  php tools\\qa\\check_no_page_group_id_in_active_seeds.php');
out('  php bin\\console seed');
out('  php tools\\qa\\run_identity_seed_cleanup_checks.php');
