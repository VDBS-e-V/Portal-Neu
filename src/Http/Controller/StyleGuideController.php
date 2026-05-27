<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class StyleGuideController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->html('pages/style-guide/index', [
            'title' => 'VDBS Portal',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Style Guide',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }

    public function icons(Request $request): Response
    {
        return $this->html('pages/style-guide/icons', [
            'title' => 'VDBS Portal',
            'areaName' => 'Style Guide',
            'pageTitle' => 'Icons',
            'areaRootLink' => '/',
            'areaNav' => [[
                'label' => 'Start',
                'href' => '/',
                'active' => true,
            ]],
            'headerNav' => [],
            'path' => $request->path,
            'now' => date('c'),
        ]);
    }
}