<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$legacyTables = [
    'ids_permission_groups',
    'ids_user_permission_groups',
    'ids_person_permission_groups',
];

require_once __DIR__ . DIRECTORY_SEPARATOR . 'identity_qa_bootstrap.php';

try {
    if (function_exists('identity_qa_pdo')) {
        $pdo = identity_qa_pdo($root);
    } elseif (function_exists('identityQaPdo')) {
        $pdo = identityQaPdo($root);
    } else {
        throw new RuntimeException('identity_qa_bootstrap.php stellt keine PDO-Funktion bereit.');
    }

    $existing = [];
    foreach ($legacyTables as $table) {
        $stmt = $pdo->prepare('SHOW TABLES LIKE :table_name');
        $stmt->execute(['table_name' => $table]);
        if ($stmt->fetchColumn()) {
            $existing[] = $table;
        }
    }

    if ($existing !== []) {
        echo "Legacy-Identity-Gruppentabellen existieren noch:\n";
        foreach ($existing as $table) {
            echo " - {$table}\n";
        }
        exit(1);
    }

    echo "OK: Legacy-Identity-Gruppentabellen sind aus der laufenden DB entfernt.\n";
} catch (Throwable $throwable) {
    fwrite(STDERR, "FEHLER: " . $throwable->getMessage() . "\n");
    exit(1);
}
