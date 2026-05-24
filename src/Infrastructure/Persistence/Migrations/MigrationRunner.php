<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Migrations;

final class MigrationRunner
{
	public function __construct(
		private \PDO $pdo,
		private string $migrationsPath
	) {}

	public function status(): array
	{
		$this->ensureMigrationsTable();

		$files = $this->listMigrationFiles();
		$applied = $this->appliedMigrationsMap();

		$out = [];
		foreach ($files as $file) {
			$name = basename($file);
			$checksum = hash_file('sha256', $file) ?: '';
			$out[] = [
				'migration' => $name,
				'applied' => array_key_exists($name, $applied),
				'checksum_ok' => !isset($applied[$name]) ? null : ($applied[$name] === $checksum),
			];
		}
		return $out;
	}

	public function migrate(): void
    {
        $this->ensureMigrationsTable();

        $files = $this->listMigrationFiles();
        $applied = $this->appliedMigrationsMap();

        foreach ($files as $file) {
            $name = basename($file);
            if (isset($applied[$name])) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new \RuntimeException("Cannot read migration file: {$file}");
            }

            $checksum = hash('sha256', $sql);

            // 1) Migration SQL laufen lassen (DDL kann implizit committen)
            $this->pdo->exec($sql);

            // 2) Als angewendet markieren
            $stmt = $this->pdo->prepare(
                "INSERT INTO migrations (migration, checksum, applied_at) VALUES (:m, :c, NOW())"
            );
            $stmt->execute([':m' => $name, ':c' => $checksum]);
        }
    }

	private function ensureMigrationsTable(): void
	{
		// Wenn die Tabelle noch nicht existiert, legen wir sie über das SQL der ersten Migration an.
		// Alternative: harte CREATE TABLE hier drin. Ich nutze die Migration selbst.
		// Wir checken aber zuerst, ob sie existiert.
		$q = $this->pdo->query("SHOW TABLES LIKE 'migrations'");
		$exists = $q && $q->fetchColumn();

		if ($exists) {
			return;
		}

		$first = $this->findFirstMigrationCreatingMigrationsTable();
		if ($first === null) {
			throw new \RuntimeException("migrations table missing and no migration found to create it.");
		}

		$sql = file_get_contents($first);
		if ($sql === false) {
			throw new \RuntimeException("Cannot read migration file: {$first}");
		}

		$this->pdo->exec($sql);
	}

	private function findFirstMigrationCreatingMigrationsTable(): ?string
	{
		foreach ($this->listMigrationFiles() as $file) {
			if (str_contains(basename($file), 'create_migrations_table')) {
				return $file;
			}
		}
		return null;
	}

	private function listMigrationFiles(): array
	{
		$pattern = rtrim($this->migrationsPath, '/\\') . DIRECTORY_SEPARATOR . '*.sql';
		$files = glob($pattern) ?: [];
		sort($files, SORT_STRING);
		return $files;
	}

	private function appliedMigrationsMap(): array
	{
		$stmt = $this->pdo->query("SELECT migration, checksum FROM migrations");
		$rows = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
		$map = [];
		foreach ($rows as $r) {
			$map[$r['migration']] = $r['checksum'];
		}
		return $map;
	}
}