<?php

// Beispiel-Ergänzung für config/routes.php.
// Nicht als eigene Datei laden und nicht die ganze routes.php ersetzen.
//
// Ziel: alte Verwaltung-URLs, die noch auf App\Http\Controller\Verwaltung\...
// zeigen, auf die neuen Administration-Controller umbiegen.
//
// Wichtig:
// - Diese Routen müssen vor alten /verwaltung/gruppen- oder /verwaltung/berechtigungen-Routen stehen,
//   oder die alten Routen müssen entfernt/ersetzt werden.
// - Falls die use-Statements für AdministrationController schon existieren, nicht doppelt importieren.

use App\Http\Routing\Route;
use App\Http\Controller\Administration\AdministrationController as AdministrationDashboardController;
use App\Http\Controller\Administration\SystemeController as AdministrationSystemeController;
use App\Http\Controller\Administration\GruppenController as AdministrationGruppenController;
use App\Http\Controller\Administration\PermissionsController as AdministrationPermissionsController;
use App\Http\Controller\Administration\PersonenGruppenController as AdministrationPersonenGruppenController;
use App\Http\Controller\Administration\SubjectsController as AdministrationSubjectsController;

return [
    // ---------------------------------------------------------------------
    // Legacy-Verwaltung-Aliase für das neue Permission-/Administration-System
    // ---------------------------------------------------------------------

    new Route('GET', '/verwaltung', AdministrationDashboardController::class, 'index'),

    new Route('GET', '/verwaltung/systeme', AdministrationSystemeController::class, 'index'),
    new Route('GET', '/verwaltung/systeme/create', AdministrationSystemeController::class, 'createForm'),
    new Route('POST', '/verwaltung/systeme/create', AdministrationSystemeController::class, 'create'),
    new Route('GET', '/verwaltung/systeme/{id}', AdministrationSystemeController::class, 'show'),
    new Route('GET', '/verwaltung/systeme/{id}/edit', AdministrationSystemeController::class, 'editForm'),
    new Route('POST', '/verwaltung/systeme/{id}/edit', AdministrationSystemeController::class, 'edit'),

    new Route('GET', '/verwaltung/gruppen', AdministrationGruppenController::class, 'index'),
    new Route('GET', '/verwaltung/gruppen/create', AdministrationGruppenController::class, 'createForm'),
    new Route('POST', '/verwaltung/gruppen/create', AdministrationGruppenController::class, 'create'),
    new Route('GET', '/verwaltung/gruppen/{id}', AdministrationGruppenController::class, 'show'),
    new Route('GET', '/verwaltung/gruppen/{id}/edit', AdministrationGruppenController::class, 'editForm'),
    new Route('POST', '/verwaltung/gruppen/{id}/edit', AdministrationGruppenController::class, 'edit'),
    new Route('GET', '/verwaltung/gruppen/{id}/permissions', AdministrationGruppenController::class, 'permissionsForm'),
    new Route('POST', '/verwaltung/gruppen/{id}/permissions', AdministrationGruppenController::class, 'permissions'),
    new Route('POST', '/verwaltung/gruppen/{id}/delete', AdministrationGruppenController::class, 'delete'),

    new Route('GET', '/verwaltung/berechtigungen', AdministrationPermissionsController::class, 'index'),
    new Route('GET', '/verwaltung/permissions', AdministrationPermissionsController::class, 'index'),
    new Route('GET', '/verwaltung/permissions/create', AdministrationPermissionsController::class, 'createForm'),
    new Route('POST', '/verwaltung/permissions/create', AdministrationPermissionsController::class, 'create'),
    new Route('GET', '/verwaltung/permissions/{id}', AdministrationPermissionsController::class, 'show'),
    new Route('GET', '/verwaltung/permissions/{id}/edit', AdministrationPermissionsController::class, 'editForm'),
    new Route('POST', '/verwaltung/permissions/{id}/edit', AdministrationPermissionsController::class, 'edit'),
    new Route('POST', '/verwaltung/permissions/{id}/deactivate', AdministrationPermissionsController::class, 'deactivate'),

    new Route('GET', '/verwaltung/personen-gruppen', AdministrationPersonenGruppenController::class, 'index'),
    new Route('GET', '/verwaltung/personen/{id}/gruppen', AdministrationPersonenGruppenController::class, 'groups'),
    new Route('POST', '/verwaltung/personen/{id}/gruppen', AdministrationPersonenGruppenController::class, 'assign'),
    new Route('POST', '/verwaltung/personen/{id}/gruppen/{groupId}/remove', AdministrationPersonenGruppenController::class, 'remove'),

    new Route('GET', '/verwaltung/subjects/{id}', AdministrationSubjectsController::class, 'show'),
];
