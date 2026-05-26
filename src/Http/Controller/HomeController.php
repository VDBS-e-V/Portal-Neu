<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->html('pages/home/index', [
            'title' => 'VDBS Portal',
            'areaName' => 'VDBS Portal',
            'pageTitle' => 'Startseite',
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

    public function health(Request $request): Response
    {
        return $this->text('ok');
    }

    public function apiHealth(Request $request): Response
    {
        return $this->json(['status' => 'ok']);
    }
}