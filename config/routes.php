<?php

declare(strict_types=1);

use App\Http\Controller\DevelopmentController;
use App\Http\Controller\AuthController;
use App\Http\Controller\HomeController;
use App\Http\Controller\NewsController;
use App\Http\Controller\StyleGuideController;
use App\Http\Controller\HealthController;
use App\Http\Controller\UserAccountController;
use App\Http\Controller\Verwaltung\VerwaltungController;
use App\Http\Controller\Verwaltung\PersonenController;
use App\Http\Controller\Verwaltung\GruppenController;
use App\Http\Controller\Verwaltung\BerechtigungenController;
use App\Http\Controller\Verwaltung\AuditLogController;
use App\Http\Controller\InvitationController;
use App\Http\Controller\Verwaltung\EinladungenController;
use App\Http\Controller\Verwaltung\DatenschutzController;
use App\Http\Controller\Verwaltung\EntityAuditController;
use App\Http\Controller\AccountController;
use App\Http\Controller\PasswordResetController;
use App\Http\Controller\ProfileController;
use App\Http\Controller\Administration\AdministrationController;
use App\Http\Controller\Administration\SystemeController;
use App\Http\Controller\Administration\PermissionsController;
use App\Http\Controller\Administration\PersonenGruppenController;
use App\Http\Controller\Administration\SubjectsController;
use App\Http\Routing\Route;

return [
    new Route('GET', '/', HomeController::class, 'index'),
    
    // Auth / user
    new Route('GET', '/login', AuthController::class, 'loginForm'),
    new Route('POST', '/login', AuthController::class, 'login'),
    new Route('POST', '/logout', AuthController::class, 'logout'),

    new Route('GET', '/api/user/me', AuthController::class, 'apiMe'),

    new Route('GET', '/health', HealthController::class, 'health'),
    new Route('GET', '/api/health', HealthController::class, 'apiHealth'),

    // News
    new Route('GET', '/news', NewsController::class, 'index'),
    new Route('GET', '/news/{id}', NewsController::class, 'show'),

    // Development web control: Dashboard
    new Route('GET', '/development/web-control', DevelopmentController::class, 'webControlDashboard'),

    // Development web control: Areas
    new Route('GET', '/development/web-control/areas', DevelopmentController::class, 'areasIndex'),

    new Route('GET', '/development/web-control/areas/create', DevelopmentController::class, 'areasCreateForm'),
    new Route('POST', '/development/web-control/areas/create', DevelopmentController::class, 'areasCreate'),

    // Neue dynamische Area-Routen
    new Route('GET', '/development/web-control/areas/{id}/edit', DevelopmentController::class, 'areasEditForm'),
    new Route('POST', '/development/web-control/areas/{id}/edit', DevelopmentController::class, 'areasEdit'),
    new Route('POST', '/development/web-control/areas/{id}/delete', DevelopmentController::class, 'areasDelete'),

    // Alte Area-Routen als Übergang, damit vorhandene Templates/Formulare weiter funktionieren
    new Route('GET', '/development/web-control/areas/edit', DevelopmentController::class, 'areasEditForm'),
    new Route('POST', '/development/web-control/areas/edit', DevelopmentController::class, 'areasEdit'),
    new Route('POST', '/development/web-control/areas/delete', DevelopmentController::class, 'areasDelete'),

    // Development web control: Menus
    new Route('GET', '/development/web-control/menus', DevelopmentController::class, 'menusIndex'),

    new Route('GET', '/development/web-control/menus/create', DevelopmentController::class, 'menusCreateForm'),
    new Route('POST', '/development/web-control/menus/create', DevelopmentController::class, 'menusCreate'),

    // Neue dynamische Menü-Routen
    new Route('GET', '/development/web-control/menus/{id}/edit', DevelopmentController::class, 'menusEditForm'),
    new Route('POST', '/development/web-control/menus/{id}/edit', DevelopmentController::class, 'menusEdit'),
    new Route('POST', '/development/web-control/menus/{id}/delete', DevelopmentController::class, 'menusDelete'),

    // Alte Menü-Routen als Übergang, damit vorhandene Templates/Formulare weiter funktionieren
    new Route('GET', '/development/web-control/menus/edit', DevelopmentController::class, 'menusEditForm'),
    new Route('POST', '/development/web-control/menus/edit', DevelopmentController::class, 'menusEdit'),
    new Route('POST', '/development/web-control/menus/delete', DevelopmentController::class, 'menusDelete'),

    // Development web control: Menu Items, neue dynamische Routen
    new Route('POST', '/development/web-control/menus/{menuId}/items/create', DevelopmentController::class, 'menuItemsCreate'),
    new Route('POST', '/development/web-control/menus/{menuId}/items/{itemId}/edit', DevelopmentController::class, 'menuItemsEdit'),
    new Route('POST', '/development/web-control/menus/{menuId}/items/{itemId}/delete', DevelopmentController::class, 'menuItemsDelete'),

    // Alte Menu-Item-Routen als Übergang
    new Route('POST', '/development/web-control/menu-items/create', DevelopmentController::class, 'menuItemsCreate'),
    new Route('POST', '/development/web-control/menu-items/edit', DevelopmentController::class, 'menuItemsEdit'),
    new Route('POST', '/development/web-control/menu-items/delete', DevelopmentController::class, 'menuItemsDelete'),

    
    // Development WCL
    new Route('GET', '/development/wcl', StyleGuideController::class, 'index'),

    new Route('GET', '/development/wcl/buttons', StyleGuideController::class, 'buttons'),
    new Route('GET', '/development/wcl/buttons/generator', StyleGuideController::class, 'buttonGenerator'),

    new Route('GET', '/development/wcl/containers', StyleGuideController::class, 'containers'),
    new Route('GET', '/development/wcl/forms', StyleGuideController::class, 'forms'),

    new Route('GET', '/development/wcl/grids', StyleGuideController::class, 'grids'),
    new Route('GET', '/development/wcl/grids/generator', StyleGuideController::class, 'gridsGenerator'),

    new Route('GET', '/development/wcl/heros', StyleGuideController::class, 'heros'),
    new Route('GET', '/development/wcl/heros/generator', StyleGuideController::class, 'herosGenerator'),

    new Route('GET', '/development/wcl/links', StyleGuideController::class, 'links'),
    new Route('GET', '/development/wcl/media', StyleGuideController::class, 'media'),

    new Route('GET', '/development/wcl/popovers', StyleGuideController::class, 'popovers'),
    new Route('GET', '/development/wcl/popovers/generator', StyleGuideController::class, 'popoversGenerator'),

    new Route('GET', '/development/wcl/summaries', StyleGuideController::class, 'summaries'),
    new Route('GET', '/development/wcl/tables', StyleGuideController::class, 'tables'),

    new Route('GET', '/development/wcl/icons', StyleGuideController::class, 'icons'),
    new Route('GET', '/development/wcl/icons/generator', StyleGuideController::class, 'iconsGenerator'),
    

    // Verwaltung: Dashboard

    new Route('GET', '/verwaltung', VerwaltungController::class, 'index'),
    

    // Verwaltung: Personen

    new Route('GET', '/verwaltung/personen', PersonenController::class, 'index'),

    new Route('GET', '/verwaltung/personen/create', PersonenController::class, 'createForm'),
    new Route('POST', '/verwaltung/personen/create', PersonenController::class, 'create'),

    new Route('GET', '/verwaltung/personen/{id}', PersonenController::class, 'show'),

    new Route('GET', '/verwaltung/personen/{id}/edit', PersonenController::class, 'editForm'),
    new Route('POST', '/verwaltung/personen/{id}/edit', PersonenController::class, 'edit'),

    new Route('POST', '/verwaltung/personen/{id}/status', PersonenController::class, 'updateStatus'),

    new Route('GET', '/verwaltung/personen/{id}/gruppen', PersonenController::class, 'groups'),
    new Route('POST', '/verwaltung/personen/{id}/gruppen', PersonenController::class, 'updateGroups'),

    new Route('GET', '/verwaltung/personen/{id}/kontakte', PersonenController::class, 'contacts'),
    new Route('POST', '/verwaltung/personen/{id}/kontakte', PersonenController::class, 'createContact'),
    new Route('POST', '/verwaltung/personen/{id}/kontakte/{contactId}/delete', PersonenController::class, 'deleteContact'),

    new Route('GET', '/verwaltung/personen/{id}/adressen', PersonenController::class, 'addresses'),
    new Route('POST', '/verwaltung/personen/{id}/adressen', PersonenController::class, 'createAddress'),
    new Route('POST', '/verwaltung/personen/{id}/adressen/{addressId}/delete', PersonenController::class, 'deleteAddress'),


    // Verwaltung: Gruppen

    new Route('GET', '/verwaltung/gruppen', GruppenController::class, 'index'),

    new Route('GET', '/verwaltung/gruppen/create', GruppenController::class, 'createForm'),
    new Route('POST', '/verwaltung/gruppen/create', GruppenController::class, 'create'),

    new Route('GET', '/verwaltung/gruppen/{id}', GruppenController::class, 'show'),

    new Route('GET', '/verwaltung/gruppen/{id}/edit', GruppenController::class, 'editForm'),
    new Route('POST', '/verwaltung/gruppen/{id}/edit', GruppenController::class, 'edit'),

    new Route('GET', '/verwaltung/gruppen/{id}/mitglieder', GruppenController::class, 'members'),

    new Route('POST', '/verwaltung/gruppen/{id}/delete', GruppenController::class, 'delete'),


    // Verwaltung: Berechtigungen

    new Route('GET', '/verwaltung/berechtigungen', BerechtigungenController::class, 'index'),

    new Route('GET', '/verwaltung/berechtigungen/page-groups', BerechtigungenController::class, 'pageGroups'),

    new Route('GET', '/verwaltung/berechtigungen/gruppen/{id}', BerechtigungenController::class, 'group'),
    new Route('POST', '/verwaltung/berechtigungen/gruppen/{id}', BerechtigungenController::class, 'updateGroup'),


    // Verwaltung: Audit

    new Route('GET', '/verwaltung/audit', AuditLogController::class, 'index'),
    new Route('GET', '/verwaltung/audit/{id}', AuditLogController::class, 'show'),


    // Verwaltung: Einladungen

    new Route('GET', '/einladung/{token}', InvitationController::class, 'acceptForm'),
    new Route('POST', '/einladung/{token}', InvitationController::class, 'accept'),

    new Route('GET', '/verwaltung/einladungen', EinladungenController::class, 'index'),
    new Route('POST', '/verwaltung/personen/{id}/einladung', EinladungenController::class, 'createForPerson'),
    new Route('POST', '/verwaltung/einladungen/{id}/revoke', EinladungenController::class, 'revoke'),


    // Verwaltung: Datenschutz

    new Route('GET', '/verwaltung/datenschutz', DatenschutzController::class, 'index'),

    new Route('GET', '/verwaltung/personen/{id}/datenschutz/loeschung', DatenschutzController::class, 'createForm'),
    new Route('POST', '/verwaltung/personen/{id}/datenschutz/loeschung', DatenschutzController::class, 'create'),

    new Route('GET', '/verwaltung/datenschutz/{id}', DatenschutzController::class, 'show'),

    new Route('POST', '/verwaltung/datenschutz/{id}/approve', DatenschutzController::class, 'approve'),
    new Route('POST', '/verwaltung/datenschutz/{id}/reject', DatenschutzController::class, 'reject'),
    new Route('POST', '/verwaltung/datenschutz/{id}/cancel', DatenschutzController::class, 'cancel'),
    new Route('POST', '/verwaltung/datenschutz/{id}/complete', DatenschutzController::class, 'complete'),

    new Route('GET', '/verwaltung/personen/{id}/audit', EntityAuditController::class, 'person'),
    new Route('GET', '/verwaltung/gruppen/{id}/audit', EntityAuditController::class, 'group'),
    new Route('GET', '/verwaltung/datenschutz/{id}/audit', EntityAuditController::class, 'erasure'),



    // Konto

    new Route('GET', '/konto', AccountController::class, 'index'),

    new Route('GET', '/konto/passwort', AccountController::class, 'passwordForm'),
    new Route('POST', '/konto/passwort', AccountController::class, 'changePassword'),

    new Route('GET', '/passwort/vergessen', PasswordResetController::class, 'requestForm'),
    new Route('POST', '/passwort/vergessen', PasswordResetController::class, 'request'),

    new Route('GET', '/passwort/zuruecksetzen/{token}', PasswordResetController::class, 'resetForm'),
    new Route('POST', '/passwort/zuruecksetzen/{token}', PasswordResetController::class, 'reset'),
    
    new Route('GET', '/konto/profil', ProfileController::class, 'profile'),

    new Route('GET', '/konto/profil/bearbeiten', ProfileController::class, 'editForm'),
    new Route('POST', '/konto/profil/bearbeiten', ProfileController::class, 'edit'),

    new Route('GET', '/konto/kontakte', ProfileController::class, 'contacts'),
    new Route('POST', '/konto/kontakte', ProfileController::class, 'createContact'),
    new Route('POST', '/konto/kontakte/{contactId}/delete', ProfileController::class, 'deleteContact'),

    new Route('GET', '/konto/adressen', ProfileController::class, 'addresses'),
    new Route('POST', '/konto/adressen', ProfileController::class, 'createAddress'),
    new Route('POST', '/konto/adressen/{addressId}/delete', ProfileController::class, 'deleteAddress'),

    new Route('GET', '/konto/sicherheit', ProfileController::class, 'security'),



    // Administration
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