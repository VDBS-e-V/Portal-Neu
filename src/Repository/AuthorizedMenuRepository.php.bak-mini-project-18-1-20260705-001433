<?php

declare(strict_types=1);

namespace App\Repository;

use App\Security\AuthorizationService;
use PDO;

/**
 * Lädt Menüeinträge und filtert sie ausschließlich über required_permission_id.
 *
 * Das alte PageGroup-Modell wird bewusst nicht mehr unterstützt.
 * Menüeinträge ohne required_permission_id bleiben sichtbar, damit rein öffentliche
 * oder strukturelle Menüpunkte weiterhin funktionieren.
 */
final class AuthorizedMenuRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly AuthorizationService $authorization
    ) {
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function flatItems(string $menuSlug): array
    {
        if (!$this->tableExists('pt_menus') || !$this->tableExists('pt_menu_items')) {
            return [];
        }

        $hasRequiredPermissionId = $this->columnExists('pt_menu_items', 'required_permission_id')
            && $this->tableExists('ids_permissions');

        $select = [
            'mi.id',
            'mi.menu_id',
            'mi.parent_id',
            'mi.title',
            'mi.slug',
            'mi.url',
            'mi.route_name',
            'mi.target',
            'mi.order_index',
            'mi.level',
            'mi.is_active',
        ];

        $join = '';
        if ($hasRequiredPermissionId) {
            $select[] = 'mi.required_permission_id';
            $select[] = 'perm.key_name AS required_permission_key';
            $join = ' LEFT JOIN ids_permissions perm ON perm.id = mi.required_permission_id AND perm.is_active = 1';
        }

        $stmt = $this->pdo->prepare(
            'SELECT ' . implode(', ', $select) . '
             FROM pt_menu_items mi
             INNER JOIN pt_menus m ON m.id = mi.menu_id
             ' . $join . '
             WHERE m.slug = :menu_slug
               AND mi.is_active = 1
             ORDER BY mi.level ASC, mi.order_index ASC, mi.title ASC, mi.id ASC'
        );
        $stmt->execute(['menu_slug' => $menuSlug]);

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_values(array_filter(
            $items,
            fn (array $item): bool => $this->canSeeItem($item)
        ));
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function tree(string $menuSlug): array
    {
        $items = $this->flatItems($menuSlug);
        $byId = [];
        $tree = [];

        foreach ($items as $item) {
            $item['children'] = [];
            $byId[(int) $item['id']] = $item;
        }

        foreach ($byId as $id => $item) {
            $parentId = (int) ($item['parent_id'] ?? 0);
            if ($parentId > 0 && isset($byId[$parentId])) {
                $byId[$parentId]['children'][] = &$byId[$id];
                continue;
            }

            $tree[] = &$byId[$id];
        }

        return $tree;
    }

    /** @param array<string,mixed> $item */
    private function canSeeItem(array $item): bool
    {
        $permissionKey = trim((string) ($item['required_permission_key'] ?? ''));
        $permissionId = (int) ($item['required_permission_id'] ?? 0);

        if ($permissionId <= 0) {
            return true;
        }

        if ($permissionKey === '') {
            return false;
        }

        return $this->authorization->can($permissionKey);
    }

    private function tableExists(string $table): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name'
        );
        $stmt->execute(['table_name' => $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name'
        );
        $stmt->execute([
            'table_name' => $table,
            'column_name' => $column,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
