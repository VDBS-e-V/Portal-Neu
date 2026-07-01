<?php

declare(strict_types=1);

namespace App\Navigation;

use App\Security\AuthorizationService;

/**
 * Kompatibilitätsklasse für alte Views/Controller, die noch VerwaltungNavigation injizieren.
 *
 * Fachlich ist die neue Oberfläche /administration. Diese Klasse gibt die neue
 * AdministrationNavigation zurück, damit alte Abhängigkeiten während des Umbaus
 * nicht sofort brechen.
 */
final class VerwaltungNavigation
{
    private AdministrationNavigation $administrationNavigation;

    public function __construct(private readonly ?AuthorizationService $authorization = null)
    {
        $this->administrationNavigation = new AdministrationNavigation($authorization);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function items(string $activeKey = ''): array
    {
        return $this->administrationNavigation->items($activeKey);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function flatItems(string $activeKey = ''): array
    {
        return $this->administrationNavigation->flatItems($activeKey);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function quickLinks(): array
    {
        return $this->administrationNavigation->quickLinks();
    }
}
