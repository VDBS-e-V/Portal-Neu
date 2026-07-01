<?php

declare(strict_types=1);

use App\Navigation\AdministrationNavigation;
use PHPUnit\Framework\TestCase;

final class AdministrationNavigationTest extends TestCase
{
    public function testNavigationWithoutAuthorizationShowsConfiguredItems(): void
    {
        $navigation = new AdministrationNavigation(null);
        $items = $navigation->items();

        self::assertNotEmpty($items);
        self::assertSame('/administration', $items[0]['href']);
        self::assertSame('portal.verwaltung.dashboard.view', $items[0]['permission']);
    }
}
