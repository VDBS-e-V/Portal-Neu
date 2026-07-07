<?php

declare(strict_types=1);

use App\Repository\IdentityMeRepository;
use PHPUnit\Framework\TestCase;

final class IdentityMeRepositoryTest extends TestCase
{
    private PDO $pdo;
    private IdentityMeRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createSchema();
        $this->seedData();
        $this->repository = new IdentityMeRepository($this->pdo);
    }

    public function testMeReturnsSubjectPersonLoginAndSystems(): void
    {
        $data = $this->repository->meForUserId(1);

        self::assertSame('subject-1', $data['subject']['uuid']);
        self::assertSame('Test User', $data['person']['display_name']);
        self::assertSame('test@example.org', $data['login']['email']);
        self::assertSame(['verwaltung'], $data['systems']['portal']['groups']);
        self::assertSame(['portal.verwaltung.personen.view'], $data['systems']['portal']['permissions']);
    }

    public function testSystemFilterReturnsOnlyRequestedSystem(): void
    {
        $data = $this->repository->meForUserId(1, 'portal');

        self::assertArrayNotHasKey('login', $data);
        self::assertSame('portal', $data['system']);
        self::assertSame(['verwaltung'], $data['groups']);
        self::assertSame(['portal.verwaltung.personen.view'], $data['permissions']);
    }

    public function testExpiredGroupsDoNotGrantPermissions(): void
    {
        $data = $this->repository->meForUserId(1, 'bibliocollect');

        self::assertSame([], $data['groups']);
        self::assertSame([], $data['permissions']);
    }

    public function testUnknownSystemThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->repository->meForUserId(1, 'unknown');
    }

    private function createSchema(): void
    {
        $this->pdo->exec('CREATE TABLE ids_subjects (id INTEGER PRIMARY KEY AUTOINCREMENT, uuid TEXT NOT NULL, status TEXT NOT NULL, permission_version INTEGER NOT NULL DEFAULT 1)');
        $this->pdo->exec('CREATE TABLE ids_persons (id INTEGER PRIMARY KEY AUTOINCREMENT, subject_id INTEGER NULL, display_name TEXT NULL)');
        $this->pdo->exec('CREATE TABLE ids_users (id INTEGER PRIMARY KEY AUTOINCREMENT, person_id INTEGER NOT NULL, email TEXT NOT NULL, status TEXT NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_systems (id INTEGER PRIMARY KEY AUTOINCREMENT, key_name TEXT NOT NULL, is_active INTEGER NOT NULL, sorting INTEGER NOT NULL DEFAULT 100)');
        $this->pdo->exec('CREATE TABLE ids_groups (id INTEGER PRIMARY KEY AUTOINCREMENT, system_id INTEGER NOT NULL, key_name TEXT NOT NULL, sorting INTEGER NOT NULL DEFAULT 100, is_active INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_permissions (id INTEGER PRIMARY KEY AUTOINCREMENT, system_id INTEGER NOT NULL, key_name TEXT NOT NULL, is_active INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_group_permissions (group_id INTEGER NOT NULL, permission_id INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_subject_groups (id INTEGER PRIMARY KEY AUTOINCREMENT, subject_id INTEGER NOT NULL, group_id INTEGER NOT NULL, assigned_at TEXT NOT NULL, expires_at TEXT NULL)');
    }

    private function seedData(): void
    {
        $this->pdo->exec("INSERT INTO ids_subjects (id, uuid, status, permission_version) VALUES (1, 'subject-1', 'active', 7)");
        $this->pdo->exec("INSERT INTO ids_persons (id, subject_id, display_name) VALUES (1, 1, 'Test User')");
        $this->pdo->exec("INSERT INTO ids_users (id, person_id, email, status) VALUES (1, 1, 'test@example.org', 'active')");

        $this->pdo->exec("INSERT INTO ids_systems (id, key_name, is_active, sorting) VALUES (1, 'portal', 1, 10), (2, 'bibliocollect', 1, 20)");
        $this->pdo->exec("INSERT INTO ids_groups (id, system_id, key_name, sorting, is_active) VALUES (1, 1, 'verwaltung', 10, 1), (2, 2, 'ausleihe', 10, 1)");
        $this->pdo->exec("INSERT INTO ids_permissions (id, system_id, key_name, is_active) VALUES (1, 1, 'portal.verwaltung.personen.view', 1), (2, 2, 'bibliocollect.ausleihe.create', 1)");
        $this->pdo->exec("INSERT INTO ids_group_permissions (group_id, permission_id) VALUES (1, 1), (2, 2)");
        $this->pdo->exec("INSERT INTO ids_subject_groups (subject_id, group_id, assigned_at, expires_at) VALUES (1, 1, CURRENT_TIMESTAMP, NULL), (1, 2, CURRENT_TIMESTAMP, '2000-01-01 00:00:00')");
    }
}
