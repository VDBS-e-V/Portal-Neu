<?php

declare(strict_types=1);

namespace App\Security;

final class VerwaltungAccess
{
    public const AREA = 'verwaltung';

    public const PERSONEN = 'personen';
    public const GRUPPEN = 'gruppen';
    public const BERECHTIGUNGEN = 'berechtigungen';

    public const ADMIN_GROUP = 'verwaltung.administrator';

    private function __construct()
    {
    }
}