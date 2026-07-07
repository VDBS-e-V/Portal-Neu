<?php

// Beispiel-Ergänzung für config/routes.php.
// Die App nutzt new Route(METHOD, PATH, CONTROLLER, ACTION).
// Bitte in die bestehende Routenliste einfügen.

use App\Http\Routing\Route;
use App\Http\Controller\Administration\AdministrationController;
use App\Http\Controller\Administration\SystemeController;
use App\Http\Controller\Administration\GruppenController;
use App\Http\Controller\Administration\PermissionsController;
use App\Http\Controller\Administration\PersonenGruppenController;
use App\Http\Controller\Administration\SubjectsController;

return [
    new Route('GET', '/administration', AdministrationController::class, 'index'),

    new Route('GET', '/administration/systeme', SystemeController::class, 'index'),
    new Route('GET', '/administration/systeme/create', SystemeController::class, 'createForm'),
    new Route('POST', '/administration/systeme/create', SystemeController::class, 'create'),
    new Route('GET', '/administration/systeme/{id}', SystemeController::class, 'show'),
    new Route('GET', '/administration/systeme/{id}/edit', SystemeController::class, 'editForm'),
    new Route('POST', '/administration/systeme/{id}/edit', SystemeController::class, 'edit'),

    new Route('GET', '/administration/gruppen', GruppenController::class, 'index'),
    new Route('GET', '/administration/gruppen/create', GruppenController::class, 'createForm'),
    new Route('POST', '/administration/gruppen/create', GruppenController::class, 'create'),
    new Route('GET', '/administration/gruppen/{id}', GruppenController::class, 'show'),
    new Route('GET', '/administration/gruppen/{id}/edit', GruppenController::class, 'editForm'),
    new Route('POST', '/administration/gruppen/{id}/edit', GruppenController::class, 'edit'),
    new Route('GET', '/administration/gruppen/{id}/permissions', GruppenController::class, 'permissionsForm'),
    new Route('POST', '/administration/gruppen/{id}/permissions', GruppenController::class, 'permissions'),
    new Route('POST', '/administration/gruppen/{id}/delete', GruppenController::class, 'delete'),

    new Route('GET', '/administration/permissions', PermissionsController::class, 'index'),
    new Route('GET', '/administration/permissions/create', PermissionsController::class, 'createForm'),
    new Route('POST', '/administration/permissions/create', PermissionsController::class, 'create'),
    new Route('GET', '/administration/permissions/{id}', PermissionsController::class, 'show'),
    new Route('GET', '/administration/permissions/{id}/edit', PermissionsController::class, 'editForm'),
    new Route('POST', '/administration/permissions/{id}/edit', PermissionsController::class, 'edit'),
    new Route('POST', '/administration/permissions/{id}/deactivate', PermissionsController::class, 'deactivate'),

    new Route('GET', '/administration/personen', PersonenGruppenController::class, 'index'),
    new Route('GET', '/administration/personen/{id}/gruppen', PersonenGruppenController::class, 'groups'),
    new Route('POST', '/administration/personen/{id}/gruppen', PersonenGruppenController::class, 'assign'),
    new Route('POST', '/administration/personen/{id}/gruppen/{groupId}/remove', PersonenGruppenController::class, 'remove'),

    new Route('GET', '/administration/subjects/{id}', SubjectsController::class, 'show'),
];
