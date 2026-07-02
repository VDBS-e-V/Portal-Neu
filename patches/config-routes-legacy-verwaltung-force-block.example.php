<?php

// Oben in config/routes.php ergänzen:
use App\Http\Controller\Administration\GruppenController as AdministrationGruppenController;
use App\Http\Controller\Administration\PermissionsController as AdministrationPermissionsController;
use App\Http\Controller\Administration\PersonenGruppenController as AdministrationPersonenGruppenController;
use App\Http\Controller\Administration\SystemeController as AdministrationSystemeController;

// Direkt am Anfang des return [ ... ] Arrays einfügen:
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

new Route('GET', '/verwaltung/systeme', AdministrationSystemeController::class, 'index'),
