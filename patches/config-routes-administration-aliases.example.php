<?php

// Beispiel-Ergänzungen für config/routes.php, falls /administration bereits auf die
// alten Verwaltung-Controller zeigen soll, bevor Mini-Projekt 3 eigene UI-Controller liefert.
// Die tatsächlichen Controller-Klassen bitte an die vorhandenen Namen im Projekt anpassen.

use App\Http\Routing\Route;
use App\Http\Controller\Verwaltung\VerwaltungController;
use App\Http\Controller\Verwaltung\PersonenController;
use App\Http\Controller\Verwaltung\GruppenController;
use App\Http\Controller\Verwaltung\BerechtigungenController;
use App\Http\Controller\Verwaltung\AuditLogController;
use App\Http\Controller\Verwaltung\EinladungenController;
use App\Http\Controller\Verwaltung\DatenschutzController;

return [
    // ... bestehende Routen ...
    new Route('GET', '/administration', VerwaltungController::class, 'index'),
    new Route('GET', '/administration/personen', PersonenController::class, 'index'),
    new Route('GET', '/administration/gruppen', GruppenController::class, 'index'),
    new Route('GET', '/administration/permissions', BerechtigungenController::class, 'index'),
    new Route('GET', '/administration/audit', AuditLogController::class, 'index'),
    new Route('GET', '/administration/einladungen', EinladungenController::class, 'index'),
    new Route('GET', '/administration/datenschutz', DatenschutzController::class, 'index'),
];
