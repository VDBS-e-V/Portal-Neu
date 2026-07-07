<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$binConsole = $projectRoot . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'console';

if (!is_file($binConsole)) {
    fwrite(STDERR, "bin/console wurde nicht gefunden: {$binConsole}\n");
    exit(1);
}

$code = file_get_contents($binConsole);
if ($code === false) {
    fwrite(STDERR, "bin/console konnte nicht gelesen werden.\n");
    exit(1);
}

$functionName = 'function replaceSeedPlaceholders';
$start = strpos($code, $functionName);
if ($start === false) {
    fwrite(STDERR, "Funktion replaceSeedPlaceholders() wurde in bin/console nicht gefunden.\n");
    exit(1);
}

$braceStart = strpos($code, '{', $start);
if ($braceStart === false) {
    fwrite(STDERR, "Funktionsrumpf von replaceSeedPlaceholders() wurde nicht gefunden.\n");
    exit(1);
}

$depth = 0;
$end = null;
$length = strlen($code);
for ($i = $braceStart; $i < $length; $i++) {
    $char = $code[$i];
    if ($char === '{') {
        $depth++;
    } elseif ($char === '}') {
        $depth--;
        if ($depth === 0) {
            $end = $i + 1;
            break;
        }
    }
}

if ($end === null) {
    fwrite(STDERR, "Ende von replaceSeedPlaceholders() wurde nicht gefunden.\n");
    exit(1);
}

$newFunction = <<<'PHP_CODE'
function replaceSeedPlaceholders(string $sql, array $options): string
{
    $readEnv = static function (string $key): ?string {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return (string) $value;
        }

        $envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($envPath) || !is_readable($envPath)) {
            return null;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return null;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $rawValue] = explode('=', $line, 2);
            $name = trim($name);
            if ($name !== $key) {
                continue;
            }

            $rawValue = trim($rawValue);
            if (
                strlen($rawValue) >= 2
                && (($rawValue[0] === '"' && substr($rawValue, -1) === '"')
                    || ($rawValue[0] === "'" && substr($rawValue, -1) === "'"))
            ) {
                $rawValue = substr($rawValue, 1, -1);
            }

            putenv($key . '=' . $rawValue);
            $_ENV[$key] = $rawValue;
            return $rawValue;
        }

        return null;
    };

    $sqlLiteral = static function (string $value): string {
        return str_replace(
            ["\\", "'", "\0", "\n", "\r", "\x1a"],
            ["\\\\", "\\'", "\\0", "\\n", "\\r", "\\Z"],
            $value
        );
    };

    if (strpos($sql, '<BCRYPT_HASH>') !== false) {
        $password = (string) (
            $options['admin-password']
            ?? $readEnv('INITIAL_ADMIN_PASSWORD')
            ?? $readEnv('SEED_ADMIN_PASSWORD')
            ?? ''
        );

        if ($password === '') {
            fwrite(STDERR, "Initial admin password is required. Use INITIAL_ADMIN_PASSWORD in .env or --admin-password=...\n");
            exit(1);
        }

        $sql = str_replace('<BCRYPT_HASH>', password_hash($password, PASSWORD_BCRYPT), $sql);
    }

    if (strpos($sql, '<INITIAL_ADMIN_NAME>') !== false) {
        $name = (string) ($options['initial-admin-name'] ?? $readEnv('INITIAL_ADMIN_NAME') ?? 'admin');
        $name = trim($name);

        if ($name === '') {
            fwrite(STDERR, "Initial admin name is required. Use INITIAL_ADMIN_NAME in .env or --initial-admin-name=...\n");
            exit(1);
        }

        $sql = str_replace('<INITIAL_ADMIN_NAME>', $sqlLiteral($name), $sql);
    }

    if (strpos($sql, '<INITIAL_ADMIN_EMAIL>') !== false) {
        $email = (string) ($options['initial-admin-email'] ?? $readEnv('INITIAL_ADMIN_EMAIL') ?? '');
        $email = trim($email);

        if ($email === '') {
            fwrite(STDERR, "Initial admin email is required. Use INITIAL_ADMIN_EMAIL in .env or --initial-admin-email=...\n");
            exit(1);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            fwrite(STDERR, "Invalid initial admin email: {$email}\n");
            exit(1);
        }

        $sql = str_replace('<INITIAL_ADMIN_EMAIL>', $sqlLiteral($email), $sql);
    }

    return $sql;
}
PHP_CODE;

$backup = $binConsole . '.bak-initial-admin-env-' . date('Ymd-His');
if (!copy($binConsole, $backup)) {
    fwrite(STDERR, "Backup konnte nicht erstellt werden.\n");
    exit(1);
}

$updated = substr($code, 0, $start) . $newFunction . substr($code, $end);
file_put_contents($binConsole, $updated);

echo "bin/console wurde angepasst.\n";
echo "Backup: {$backup}\n";
echo "Unterstützte .env-Werte:\n";
echo "  INITIAL_ADMIN_NAME\n";
echo "  INITIAL_ADMIN_EMAIL\n";
echo "  INITIAL_ADMIN_PASSWORD\n";
