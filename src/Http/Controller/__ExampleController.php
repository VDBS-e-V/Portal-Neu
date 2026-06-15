<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Request\Request;
use App\Http\Response\Response;

final class ExampleController extends PageController
{
    public function index(Request $request): Response
    {
        return $this->renderPage($request, 'pages/example/index', [
            'title' => 'Beispiel',
            'pageTitle' => 'Beispiel',
            'areaName' => 'VDBS Portal',
            'areaRootLink' => '/',
            'headerAreaKey' => 'portal',
        ]);
    }

    public function show(Request $request): Response
    {
        return $this->renderPage($request, 'pages/example/show', [
            'title' => 'Beispiel Detail',
            'pageTitle' => 'Beispiel Detail',
            'areaName' => 'VDBS Portal',
            'areaRootLink' => '/',
            'headerAreaKey' => 'portal',
            'id' => $this->routeInt($request, 'id'),
        ]);
    }

    public function form(Request $request): Response
    {
        return $this->renderPage($request, 'pages/example/form', [
            'title' => 'Beispiel Formular',
            'pageTitle' => 'Beispiel Formular',
            'areaName' => 'VDBS Portal',
            'areaRootLink' => '/',
            'headerAreaKey' => 'portal',
            'errors' => [],
        ]);
    }

    public function save(Request $request): Response
    {
        $title = $this->bodyString($request->body, 'title');

        if ($title === '') {
            return $this->renderPage($request, 'pages/example/form', [
                'title' => 'Beispiel Formular',
                'pageTitle' => 'Beispiel Formular',
                'areaName' => 'VDBS Portal',
                'areaRootLink' => '/',
                'headerAreaKey' => 'portal',
                'errors' => ['Titel ist erforderlich.'],
            ], 422);
        }

        // Hier später speichern, z. B. über ein Repository.

        return $this->redirect('/example');
    }
}