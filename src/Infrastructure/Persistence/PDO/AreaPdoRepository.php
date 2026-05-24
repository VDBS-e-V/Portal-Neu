<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\PDO;

use App\Domain\Area\Entity\Area;
use App\Domain\Area\Repository\AreaRepository;
use App\Domain\User\Entity\User;

final class AreaPdoRepository implements AreaRepository
{
    public function __construct(private \PDO $pdo) {}

    /** @return Area[] */
    public function all(): array
    {
        return $this->findAllOrdered();
    }

    /** @return Area[] */
    public function findAllOrdered(): array
    {
        $sql = 'SELECT id, uuid, name, slug, description, icon, sorting, allowed_user_groups, visibility_header, visibility_listing, created_at, updated_at FROM ids_areas WHERE deleted_at IS NULL ORDER BY sorting ASC, id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $areas = [];
        foreach ($rows as $r) {
            $areas[] = $this->mapRowToArea($r);
        }
        return $areas;
    }

    /** @return Area[] */
    public function findVisibleForUser(User $user): array
    {
        // fetch user permissions JSON from ids_users.permissions as fallback-source for groups/roles
        $stmt = $this->pdo->prepare('SELECT permissions FROM ids_users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $user->id]);
        $permJson = $stmt->fetchColumn();
        $perms = [];
        if ($permJson) {
            $decoded = json_decode($permJson, true);
            if (is_array($decoded)) {
                $perms = $decoded;
            }
        }

        // build candidate group/role list: keys + roles
        $userGroups = [];
        foreach ($perms as $key => $roles) {
            $userGroups[] = (string)$key;
            if (is_array($roles)) {
                foreach ($roles as $r) {
                    $userGroups[] = (string)$r;
                }
            }
        }
        $userGroups = array_values(array_unique(array_filter($userGroups)));

        $areas = $this->findAllOrdered();
        $visible = [];
        foreach ($areas as $area) {
            if (empty($area->allowedUserGroups)) {
                $visible[] = $area;
                continue;
            }
            // allowedUserGroups is expected to be an array of strings
            $intersect = array_intersect($area->allowedUserGroups, $userGroups);
            if (!empty($intersect)) {
                $visible[] = $area;
            }
        }
        return $visible;
    }

    private function mapRowToArea(array $r): Area
    {
        $allowed = [];
        if (!empty($r['allowed_user_groups'])) {
            $decoded = json_decode($r['allowed_user_groups'], true);
            if (is_array($decoded)) $allowed = $decoded;
        }

        return new Area(
            (int)$r['id'],
            (string)$r['uuid'],
            (string)$r['name'],
            (string)$r['slug'],
            $r['description'] ?? null,
            $r['icon'] ?? null,
            (int)$r['sorting'],
            $allowed,
            (bool)$r['visibility_header'],
            (bool)$r['visibility_listing'],
            $r['created_at'] ?? null,
            $r['updated_at'] ?? null,
        );
    }
}
