<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Bootstrap\Container;
use App\Http\Request\Request;
use App\Http\Response\HtmlResponse;
use App\Presentation\Templating\Renderer;
use App\Infrastructure\Persistence\PDO\AreaPdoRepository;
use App\Infrastructure\Persistence\PDO\MenuPdoRepository;
use PDO;

final class HomeController
{
    public function __construct(private array $container) {}

    public function index(Request $request): HtmlResponse
    {
        /** @var Renderer $renderer */
        $renderer = Container::get($this->container, Renderer::class);

        $html = $renderer->renderPage('pages/home/index', [
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Startseite',
            'areaRootLink' => '/',
            'areaNav' => $this->buildAreaNav($request),
            'headerNav' => $this->buildHeaderNav($request),
            'now' => date('c'),
            'path' => $request->path,
        ]);

        return new HtmlResponse($html);
    }

    private function buildAreaNav(Request $request): array
    {
        $dbConfig = Container::get($this->container, 'config.db');
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbConfig['host'], (int)$dbConfig['port'], $dbConfig['name'], $dbConfig['charset']);
        try {
            $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (\Throwable $e) {
            // fallback: simple static nav
            return [
                ['label' => 'Start', 'href' => '/', 'active' => $request->path === '/'],
                ['label' => 'Styleguide', 'href' => '/styleguide', 'active' => $request->path === '/styleguide'],
            ];
        }

        $repo = new AreaPdoRepository($pdo);
        $areas = $repo->findAllOrdered();

        $nav = [];
        foreach ($areas as $a) {
            $slug = $a->slug;
            if (str_starts_with($slug, '/')) {
                $href = $slug;
            } elseif ($slug === 'start' || $slug === '') {
                $href = '/';
            } else {
                $href = '/' . ltrim($slug, '/');
            }

            $active = false;
            if ($href === '/') {
                $active = $request->path === '/';
            } else {
                $active = str_starts_with($request->path, $href);
            }

            $nav[] = [
                'label' => $a->name,
                'href' => $href,
                'active' => $active,
                'icon' => $a->icon ?? null,
            ];
        }

        return $nav;
    }

    private function buildHeaderNav(Request $request): array
    {
        $fallback = [
            ['label' => 'Über das Portal', 'href' => '/ueber-das-portal', 'active' => false],
            ['label' => 'Zugang zum Portal', 'href' => '/zugang-zum-portal', 'active' => false],
            ['label' => 'FAQ', 'href' => '/faq', 'active' => false],
            ['label' => 'Kontakt', 'href' => '/kontakt', 'active' => false],
        ];

        try {
            /** @var MenuPdoRepository $menuRepo */
            $menuRepo = Container::get($this->container, MenuPdoRepository::class);
        } catch (\Throwable $e) {
            return $fallback;
        }

        // determine current area id by reusing area loading logic
        $dbConfig = Container::get($this->container, 'config.db');
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbConfig['host'], (int)$dbConfig['port'], $dbConfig['name'], $dbConfig['charset']);
        try {
            $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (\Throwable $e) {
            return $fallback;
        }

        $areaRepo = new AreaPdoRepository($pdo);
        $areas = $areaRepo->findAllOrdered();
        $currentAreaId = null;
        foreach ($areas as $a) {
            $slug = $a->slug;
            if (str_starts_with($slug, '/')) {
                $href = $slug;
            } elseif ($slug === 'start' || $slug === '') {
                $href = '/';
            } else {
                $href = '/' . ltrim($slug, '/');
            }

            $active = false;
            if ($href === '/') {
                $active = $request->path === '/';
            } else {
                $active = str_starts_with($request->path, $href);
            }

            if ($active) {
                $currentAreaId = $a->id;
                break;
            }
        }

        if ($currentAreaId === null) return $fallback;

        $menu = $menuRepo->findByAreaId($currentAreaId);
        if ($menu === null) return $fallback;

        // build nested tree and map it to arrays suitable for the view
        $tree = $menuRepo->findItemsTreeByMenuId($menu->id);

        $mapNode = null;
        $mapNode = function (array $node) use (&$mapNode, $request) : array {
            $item = $node['item'];
            $children = [];
            $active = false;

            foreach ($node['children'] as $child) {
                $childMapped = $mapNode($child);
                if ($childMapped['active']) $active = true;
                $children[] = $childMapped;
            }

            $href = $item->href ?? '#';
            if (!$active) {
                if ($href === '/') {
                    $active = $request->path === '/';
                } else {
                    $active = str_starts_with($request->path, (string)$href);
                }
            }

            return [
                'label' => $item->title,
                'href' => $href,
                'active' => $active,
                'children' => $children,
            ];
        };

        $header = [];
        foreach ($tree as $node) {
            if (!$node['item']->visible) continue;
            $header[] = $mapNode($node);
        }

        return empty($header) ? $fallback : $header;
    }
}