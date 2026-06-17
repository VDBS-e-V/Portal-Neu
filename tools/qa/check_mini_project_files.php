<?php

declare(strict_types=1);

require_once __DIR__ . '/PortalQa.php';

$root = PortalQa::projectRoot();
$failures = [];

$expectedFiles = [
    'src/Security/AuthorizationService.php',
    'src/Security/AdminSafetyService.php',
    'src/Audit/AuditLogger.php',

    'src/Repository/PersonRepository.php',
    'src/Repository/PersonContactRepository.php',
    'src/Repository/PersonAddressRepository.php',

    'src/Http/Controller/Verwaltung/PersonenController.php',
    'src/Http/Controller/Verwaltung/GruppenController.php',
    'src/Http/Controller/Verwaltung/BerechtigungenController.php',
    'src/Http/Controller/Verwaltung/AuditLogController.php',

    'src/Repository/UserInvitationRepository.php',
    'src/Http/Controller/InvitationController.php',
    'src/Http/Controller/Verwaltung/EinladungenController.php',

    'src/Repository/PersonErasureRepository.php',
    'src/Http/Controller/Verwaltung/DatenschutzController.php',

    'src/Repository/VerwaltungStatsRepository.php',
    'src/Http/Controller/Verwaltung/VerwaltungController.php',
    'src/Navigation/VerwaltungNavigation.php',

    'src/Security/CsrfGuard.php',
    'src/Security/CsrfTokenManager.php',

    'src/Repository/UserPasswordRepository.php',
    'src/Repository/PasswordResetRepository.php',
    'src/Http/Controller/AccountController.php',
    'src/Http/Controller/PasswordResetController.php',

    'src/Repository/AccountProfileRepository.php',
    'src/Repository/LoginEventRepository.php',
    'src/Http/Controller/ProfileController.php',

    'src/Repository/EntityAuditRepository.php',
    'src/Http/Controller/Verwaltung/EntityAuditController.php',
];

foreach ($expectedFiles as $file) {
    PortalQa::assertTrue(
        is_file($root . '/' . $file),
        'Datei vorhanden: ' . $file,
        $failures
    );
}

exit($failures === [] ? 0 : 1);
