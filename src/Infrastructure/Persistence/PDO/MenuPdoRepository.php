<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\PDO;

use App\Domain\Menu\Entity\Menu;
use App\Domain\Menu\Entity\MenuItem;
use App\Domain\Menu\Repository\MenuRepository;

final class MenuPdoRepository implements MenuRepository
{
    public function __construct(private \PDO $pdo) {}

    /** @return Menu[] */
    public function all(): array
    {
        $sql = 'SELECT id, uuid, area_id, name, slug, description, href, website_paths, icon, sorting, allowed_user_groups, visibility_header, visibility_listing, menu_type, created_at, updated_at FROM ids_menus WHERE deleted_at IS NULL ORDER BY sorting ASC, id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $menus = [];
        foreach ($rows as $r) {
            $menus[] = $this->mapRowToMenu($r);
        }
        return $menus;
    }

    public function findByAreaId(int $areaId): ?Menu
    {
        $sql = 'SELECT id, uuid, area_id, name, slug, description, href, website_paths, icon, sorting, allowed_user_groups, visibility_header, visibility_listing, menu_type, created_at, updated_at FROM ids_menus WHERE area_id = :area_id AND deleted_at IS NULL LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['area_id' => $areaId]);
        $r = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return null;
        return $this->mapRowToMenu($r);
    }

    /** @return MenuItem[] */
    public function findItemsByMenuId(int $menuId): array
    {
        $sql = 'SELECT id, uuid, menu_id, parent_id, title, slug, href, attributes, sorting, visible, created_at, updated_at FROM ids_menu_items WHERE menu_id = :menu_id AND deleted_at IS NULL ORDER BY sorting ASC, id ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['menu_id' => $menuId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $items = [];
        foreach ($rows as $r) {
            $items[] = $this->mapRowToMenuItem($r);
        }
        return $items;
    }

    /**
     * @return array Nested tree: [ ['item' => MenuItem, 'children' => [...]], ... ]
     */
    public function findItemsTreeByMenuId(int $menuId): array
    {
        $flat = $this->findItemsByMenuId($menuId);
        $byId = [];
        foreach ($flat as $item) {
            $byId[$item->id] = ['item' => $item, 'children' => []];
        }

        $tree = [];
        foreach ($byId as $id => &$node) {
            $parentId = $node['item']->parentId;
            if ($parentId === null) {
                $tree[] = &$node;
            } elseif (isset($byId[$parentId])) {
                $byId[$parentId]['children'][] = &$node;
            } else {
                $tree[] = &$node; // orphan fallback
            }
        }
        return $tree;
    }

    private function mapRowToMenu(array $r): Menu
    {
        $websitePaths = [];
        if (!empty($r['website_paths'])) {
            $decoded = json_decode($r['website_paths'], true);
            if (is_array($decoded)) $websitePaths = $decoded;
        }
        $allowed = [];
        if (!empty($r['allowed_user_groups'])) {
            $decoded = json_decode($r['allowed_user_groups'], true);
            if (is_array($decoded)) $allowed = $decoded;
        }

        return new Menu(
            (int)$r['id'],
            (string)$r['uuid'],
            (int)$r['area_id'],
            (string)$r['name'],
            (string)$r['slug'],
            $r['description'] ?? null,
            $r['href'] ?? null,
            $websitePaths,
            $r['icon'] ?? null,
            (int)$r['sorting'],
            $allowed,
            (bool)$r['visibility_header'],
            (bool)$r['visibility_listing'],
            $r['menu_type'] ?? 'header',
            $r['created_at'] ?? null,
            $r['updated_at'] ?? null,
        );
    }

    private function mapRowToMenuItem(array $r): MenuItem
    {
        $attributes = [];
        if (!empty($r['attributes'])) {
            $decoded = json_decode($r['attributes'], true);
            if (is_array($decoded)) $attributes = $decoded;
        }

        return new MenuItem(
            (int)$r['id'],
            (string)$r['uuid'],
            (int)$r['menu_id'],
            isset($r['parent_id']) && $r['parent_id'] !== null ? (int)$r['parent_id'] : null,
            (string)$r['title'],
            $r['slug'] ?? null,
            $r['href'] ?? null,
            $attributes,
            (int)$r['sorting'],
            (bool)$r['visible'],
            $r['created_at'] ?? null,
            $r['updated_at'] ?? null,
        );
    }
}
