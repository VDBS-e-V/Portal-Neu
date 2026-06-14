<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class StyleGuideController extends PageController
{
    private const WCL_BASE_PATH = '/development/wcl';

    public function index(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/index', 'Übersicht', 'index');
    }

    public function buttons(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/buttons', 'Buttons', 'buttons');
    }

    public function buttonGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/buttons-generator', 'Buttons Generator', 'buttons');
    }

    public function icons(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/icons', 'Icons', 'icons');
    }

    public function iconsGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/icons-generator', 'Icons Generator', 'icons');
    }

    public function containers(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/containers', 'Containers', 'containers');
    }

    public function forms(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/forms', 'Forms', 'forms');
    }

    public function grids(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/grids', 'Grids', 'grids');
    }

    public function gridsGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/grids-generator', 'Grids Generator', 'grids');
    }

    public function heros(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/heros', 'Heros', 'heros');
    }

    public function herosGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/heros-generator', 'Heros Generator', 'heros');
    }

    public function links(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/links', 'Links', 'links');
    }

    public function media(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/media', 'Media', 'media');
    }

    public function popovers(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/popovers', 'Popovers', 'popovers');
    }

    public function popoversGenerator(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/popovers-generator', 'Popovers Generator', 'popovers');
    }

    public function summaries(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/summaries', 'Summaries', 'summaries');
    }

    public function tables(Request $request): Response
    {
        return $this->stylePage($request, 'pages/development/wcl/tables', 'Tables', 'tables');
    }

    private function stylePage(Request $request, string $view, string $title, string $activeKey): Response
    {
        return $this->page($request, $view, [
            'title' => 'WCL · ' . $title,
            'areaName' => 'Web-Control',
            'pageTitle' => 'WCL · ' . $title,
            'areaRootLink' => self::WCL_BASE_PATH,
            'headerAreaKey' => 'development',
            'areaNav' => $this->wclNav($activeKey),
        ]);
    }

    private function wclNav(string $activeKey): array
    {
        return [
            [
                'label' => 'Übersicht',
                'href' => self::WCL_BASE_PATH,
                'active' => $activeKey === 'index',
            ],
            [
                'label' => 'Buttons',
                'href' => self::WCL_BASE_PATH . '/buttons',
                'active' => $activeKey === 'buttons',
            ],
            [
                'label' => 'Containers',
                'href' => self::WCL_BASE_PATH . '/containers',
                'active' => $activeKey === 'containers',
            ],
            [
                'label' => 'Forms',
                'href' => self::WCL_BASE_PATH . '/forms',
                'active' => $activeKey === 'forms',
            ],
            [
                'label' => 'Grids',
                'href' => self::WCL_BASE_PATH . '/grids',
                'active' => $activeKey === 'grids',
            ],
            [
                'label' => 'Heros',
                'href' => self::WCL_BASE_PATH . '/heros',
                'active' => $activeKey === 'heros',
            ],
            [
                'label' => 'Links',
                'href' => self::WCL_BASE_PATH . '/links',
                'active' => $activeKey === 'links',
            ],
            [
                'label' => 'Media',
                'href' => self::WCL_BASE_PATH . '/media',
                'active' => $activeKey === 'media',
            ],
            [
                'label' => 'Popovers',
                'href' => self::WCL_BASE_PATH . '/popovers',
                'active' => $activeKey === 'popovers',
            ],
            [
                'label' => 'Summaries',
                'href' => self::WCL_BASE_PATH . '/summaries',
                'active' => $activeKey === 'summaries',
            ],
            [
                'label' => 'Tables',
                'href' => self::WCL_BASE_PATH . '/tables',
                'active' => $activeKey === 'tables',
            ],
            [
                'label' => 'Icons',
                'href' => self::WCL_BASE_PATH . '/icons',
                'active' => $activeKey === 'icons',
            ],
        ];
    }
}