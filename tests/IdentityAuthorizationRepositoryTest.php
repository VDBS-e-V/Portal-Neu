<?php

declare(strict_types=1);

use App\Repository\IdentityAuthorizationRepository;
use PHPUnit\Framework\TestCase;

final class IdentityAuthorizationRepositoryTest extends TestCase
{
    private PDO $pdo;
    private IdentityAuthorizationRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createSchema();
        $this->seedData();
        $this->repository = new IdentityAuthorizationRepository($this->pdo);
    }

    public function testSubjectPermissionIsResolvedThroughActiveGroup(): void
    {
        self::assertSame(1, $this->repository->subjectIdForUserId(1));
        self::assertTrue($this->repository->canSubject(1, 'portal.verwaltung.personen.view'));
        self::assertFalse($this->repository->canSubject(1, 'identity.gruppen.view'));
    }

    public function testExpiredGroupDoesNotGrantPermissions(): void
    {
        self::assertFalse($this->repository->canSubject(1, 'bibliocollect.ausleihe.create'));
    }

    public function testSystemFilterReturnsOnlySystemPermissions(): void
    {
        self::assertSame(
            ['portal.verwaltung.personen.view'],
            $this->repository->permissionsForSubjectAndSystem(1, 'portal')
        );
    }

    public function testGroupsAreReturnedBySystem(): void
    {
        self::assertSame(['verwaltung'], $this->repository->groupsForSubjectAndSystem(1, 'portal'));
    }

    private function createSchema(): void
    {
        $this->pdo->exec('CREATE TABLE ids_subjects (id INTEGER PRIMARY KEY AUTOINCREMENT, uuid TEXT NOT NULL, status TEXT NOT NULL, permission_version INTEGER NOT NULL DEFAULT 1)');
        $this->pdo->exec('CREATE TABLE ids_persons (id INTEGER PRIMARY KEY AUTOINCREMENT, subject_id INTEGER NULL, person_uuid BLOB NOT NULL, display_name TEXT NULL, status TEXT NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_users (id INTEGER PRIMARY KEY AUTOINCREMENT, person_id INTEGER NOT NULL, email TEXT NOT NULL, status TEXT NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_systems (id INTEGER PRIMARY KEY AUTOINCREMENT, key_name TEXT NOT NULL, is_active INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_groups (id INTEGER PRIMARY KEY AUTOINCREMENT, system_id INTEGER NOT NULL, key_name TEXT NOT NULL, sorting INTEGER NOT NULL DEFAULT 100, is_active INTEGER NOT NULL, is_default INTEGER NOT NULL DEFAULT 0, is_assignable INTEGER NOT NULL DEFAULT 1)');
        $this->pdo->exec('CREATE TABLE ids_permissions (id INTEGER PRIMARY KEY AUTOINCREMENT, system_id INTEGER NOT NULL, key_name TEXT NOT NULL, is_active INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_group_permissions (group_id INTEGER NOT NULL, permission_id INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE ids_subject_groups (id INTEGER PRIMARY KEY AUTOINCREMENT, subject_id INTEGER NOT NULL, group_id INTEGER NOT NULL, assigned_by_subject_id INTEGER NULL, assigned_at TEXT NOT NULL, expires_at TEXT NULL, note TEXT NULL)');
    }

    private function seedData(): void
    {
        $this->pdo->exec("INSERT INTO ids_subjects (id, uuid, status, permission_version) VALUES (1, 'subject-1', 'active', 1)");
        $this->pdo->exec("INSERT INTO ids_persons (id, subject_id, person_uuid, display_name, status) VALUES (1, 1, x'00000000000000000000000000000001', 'Test User', 'active')");
        $this->pdo->exec("INSERT INTO ids_users (id, person_id, email, status) VALUES (1, 1, 'test@example.org', 'active')");

        $this->pdo->exec("INSERT INTO ids_systems (id, key_name, is_active) VALUES (1, 'portal', 1), (2, 'identity', 1), (3, 'bibliocollect', 1)");
        $this->pdo->exec("INSERT INTO ids_groups (id, system_id, key_name, sorting, is_active) VALUES (1, 1, 'verwaltung', 10, 1), (2, 2, 'rechteverwaltung', 10, 1), (3, 3, 'ausleihe', 10, 1)");
        $this->pdo->exec("INSERT INTO ids_permissions (id, system_id, key_name, is_active) VALUES (1, 1, 'portal.verwaltung.personen.view', 1), (2, 2, 'identity.gruppen.view', 1), (3, 3, 'bibliocollect.ausleihe.create', 1)");
        $this->pdo->exec("INSERT INTO ids_group_permissions (group_id, permission_id) VALUES (1, 1), (2, 2), (3, 3)");
        $this->pdo->exec("INSERT INTO ids_subject_groups (subject_id, group_id, assigned_at, expires_at) VALUES (1, 1, CURRENT_TIMESTAMP, NULL), (1, 3, CURRENT_TIMESTAMP, '2000-01-01 00:00:00')");
    }
}
