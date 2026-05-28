<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class StyleGuideController extends Controller
{
    private function renderStyleGuidePage(Request $request, string $pageTitle, string $view, array $data = []): Response
    {
        $base = [
            'title' => 'VDBS Portal',
            'areaName' => 'Style Guide',
            'pageTitle' => $pageTitle,
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'path' => $request->path,
            'now' => date('c'),
        ];

        return $this->html($view, $base + $data);
    }

    public function index(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Style Guide', 'pages/style-guide/index');
    }

    public function icons(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Icons', 'pages/style-guide/icons');
    }

    public function iconGenerator(Request $request): Response
    {
        $icon = (string) ($request->query['icon'] ?? 'icon-star');
        $size = (string) ($request->query['size'] ?? 'vdb-icon--xl');
        $color = (string) ($request->query['color'] ?? 'vdb-icon--primary');
        $stroke = (string) ($request->query['stroke'] ?? 'vdb-icon--regular');
        return $this->renderStyleGuidePage($request, 'Icon Generator', 'pages/style-guide/icons-generator', [
            'prefill' => [
                'icon' => $icon,
                'size' => $size,
                'color' => $color,
                'stroke' => $stroke,
            ],
        ]);
    }

    public function buttons(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Buttons', 'pages/style-guide/buttons');
    }

    public function buttonGenerator(Request $request): Response
    {
        $variant = (string) ($request->query['variant'] ?? 'vdb-button--primary');
        $size = (string) ($request->query['size'] ?? 'vdb-button--md');
        $disabled = (bool) ($request->query['disabled'] ?? false);
        return $this->renderStyleGuidePage($request, 'Button Generator', 'pages/style-guide/buttons-generator', [
            'prefill' => [
                'variant' => $variant,
                'size' => $size,
                'disabled' => $disabled,
            ],
        ]);
    }

    public function cards(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Cards', 'pages/style-guide/cards');
    }

    public function containers(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Containers', 'pages/style-guide/containers');
    }

    public function errorPages(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Error Pages', 'pages/style-guide/error-pages');
    }

    public function forms(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Forms', 'pages/style-guide/forms');
    }

    public function grids(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Grids', 'pages/style-guide/grids');
    }

    public function hero(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Hero', 'pages/style-guide/hero');
    }

    public function links(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Links', 'pages/style-guide/links');
    }

    public function lists(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Lists', 'pages/style-guide/lists');
    }

    public function popUps(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Pop-ups', 'pages/style-guide/pop-ups');
    }

    public function search(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Search', 'pages/style-guide/search');
    }

    public function sections(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Sections', 'pages/style-guide/sections');
    }

    public function statsGrid(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Stats Grid', 'pages/style-guide/stats-grid');
    }

    public function statusMessages(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Status Messages', 'pages/style-guide/status-messages');
    }

    public function submenu(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Submenu', 'pages/style-guide/submenu');
    }

    public function tables(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Tables', 'pages/style-guide/tables');
    }

    public function blogPost(Request $request): Response
    {
        return $this->renderStyleGuidePage($request, 'Blog Post', 'pages/style-guide/blog-post');
    }
}