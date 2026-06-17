<?php

declare(strict_types=1);

namespace App\Repository;

use App\Security\AuthorizationService;
use PDO;

final class AuthorizedMenuRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly AuthorizationService $authorization
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function flatItems(string $menuSlug): array
    {
        if (!$this->tableExists('pt_menus') || !$this->tableExists('pt_menu_items')) {
            return [];
        }

        $hasPageGroupId = $this->columnExists('pt_menu_items', 'page_group_id')
            && $this->tableExists('pt_page_groups')
            && $this->tableExists('pt_areas');

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

        if ($hasPageGroupId) {
            $select[] = 'mi.page_group_id';
            $select[] = 'pg.page_group_key';
            $select[] = 'area.area_key';
            $join = '
                LEFT JOIN pt_page_groups pg ON pg.id = mi.page_group_id
                LEFT JOIN pt_areas area ON area.id = pg.area_id';
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
     * @return array<int, array<string, mixed>>
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

    /**
     * @param array<string, mixed> $item
     */
    private function canSeeItem(array $item): bool
    {
        $areaKey = trim((string) ($item['area_key'] ?? ''));
        $pageGroupKey = trim((string) ($item['page_group_key'] ?? ''));

        if ($areaKey === '' || $pageGroupKey === '') {
            return true;
        }

        return $this->authorization->currentUserCanAccessPageGroup($areaKey, $pageGroupKey);
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
