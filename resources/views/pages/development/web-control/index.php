<?php declare(strict_types=1);

$stats = $stats ?? [];
$areas = $areas ?? [];
$menus = $menus ?? [];
$menuItems = $menuItems ?? [];
$menuItemsByMenuId = $menuItemsByMenuId ?? [];

$areasTotal = (int) ($stats['areas_total'] ?? 0);
$areasActive = (int) ($stats['areas_active'] ?? 0);
$areasExternal = (int) ($stats['areas_external'] ?? 0);
$menusTotal = (int) ($stats['menus_total'] ?? 0);
$menuItemsTotal = (int) ($stats['menu_items_total'] ?? 0);
$menuItemsActive = (int) ($stats['menu_items_active'] ?? 0);

$recentAreas = array_slice($areas, 0, 5);
$recentMenus = array_slice($menus, 0, 5);
?>

<section class="section--page-title section--page-title-compact">
    <header class="page-title page-title--band page-title--secondary-cta page-title--split">
        <div class="page-title__main">
            <nav class="page-title__breadcrumb" aria-label="Breadcrumb">
                <ol class="page-title__breadcrumb-list">
                    <li>
                        <a href="/">
                            Start
                        </a>
                    </li>

                    <li aria-current="page">
                        Web-Control
                    </li>
                </ol>
            </nav>

            <p class="page-title__kicker">
                Portalverwaltung
            </p>

            <h1 class="page-title__title">
                <?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <p class="page-title__lead">
                Verwalten Sie zentrale Portalbereiche, Menüs und Navigationspunkte
                an einer Stelle. Dieses Dashboard gibt Ihnen einen schnellen Überblick
                über die aktuelle Struktur des Web-Control-Bereichs.
            </p>

            <div class="page-title__badges" aria-label="Bereichsinformationen">
                <span class="page-title__badge">
                    Web-Control
                </span>

                <span class="page-title__badge">
                    Dashboard
                </span>

                <span class="page-title__badge">
                    Portalstruktur
                </span>
            </div>
        </div>

        <div class="page-title__side">
            <div class="page-title__actions btn-group btn-group--horizontal">
                <a
                    href="/development/web-control/areas/create"
                    class="btn btn--primary btn--md"
                >
                    Bereich erstellen
                </a>

                <a
                    href="/development/web-control/menus/create"
                    class="btn btn--primary-transp btn--md"
                >
                    Menü erstellen
                </a>
            </div>
        </div>
    </header>
</section>

<section class="web-control-dashboard">
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">
                        Schnellzugriff
                    </p>

                    <h2 class="table-block__title">
                        Verwaltung öffnen
                    </h2>

                    <p class="table-block__subtitle">
                        Wählen Sie einen Verwaltungsbereich aus, um Inhalte zu bearbeiten
                        oder neue Strukturelemente anzulegen.
                    </p>
                </div>
            </div>

            <div class="form">
                <div class="form__grid form__grid--3">
                    <div class="form__notice form__notice--info">
                        <strong>Bereiche</strong>
                        <p class="form__hint">
                            Portalbereiche verwalten, Startpfade festlegen und Icons pflegen.
                        </p>

                        <div class="form__actions">
                            <a
                                href="/development/web-control/areas"
                                class="btn btn--primary btn--md"
                            >
                                Bereiche öffnen
                            </a>
                        </div>
                    </div>

                    <div class="form__notice form__notice--success">
                        <strong>Menüs</strong>
                        <p class="form__hint">
                            Menüs je Bereich verwalten und Navigationsstrukturen bearbeiten.
                        </p>

                        <div class="form__actions">
                            <a
                                href="/development/web-control/menus"
                                class="btn btn--primary btn--md"
                            >
                                Menüs öffnen
                            </a>
                        </div>
                    </div>

                    <div class="form__notice form__notice--warning">
                        <strong>Navigation</strong>
                        <p class="form__hint">
                            Menu Items werden innerhalb eines Menüs erstellt und gepflegt.
                        </p>

                        <div class="form__actions">
                            <a
                                href="/development/web-control/menus"
                                class="btn btn--primary-transp btn--md"
                            >
                                Items verwalten
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">
                        Kennzahlen
                    </p>

                    <h2 class="table-block__title">
                        Portalstruktur auf einen Blick
                    </h2>

                    <p class="table-block__subtitle">
                        Kurzer Überblick über Areas, Menüs und Navigationspunkte.
                    </p>
                </div>
            </div>

            <div class="table-wrapper table-wrapper--bordered">
                <table class="table table--compact table--striped table--hover table--stack">
                    <caption>
                        Kennzahlen des Web-Control-Dashboards.
                    </caption>

                    <thead>
                        <tr>
                            <th>
                                Bereich
                            </th>

                            <th class="table__cell--right">
                                Gesamt
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Aktion
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td
                                class="table__cell--strong"
                                data-label="Bereich"
                            >
                                Areas
                                <span class="table__subtext">
                                    Davon <?= htmlspecialchars((string) $areasActive, ENT_QUOTES, 'UTF-8') ?> aktiv
                                    und <?= htmlspecialchars((string) $areasExternal, ENT_QUOTES, 'UTF-8') ?> extern.
                                </span>
                            </td>

                            <td
                                class="table__cell--right"
                                data-label="Gesamt"
                            >
                                <?= htmlspecialchars((string) $areasTotal, ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td data-label="Status">
                                <?php if ($areasTotal > 0): ?>
                                    <span class="table-badge table-badge--success">
                                        Angelegt
                                    </span>
                                <?php else: ?>
                                    <span class="table-badge table-badge--warning">
                                        Leer
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td data-label="Aktion">
                                <a
                                    href="/development/web-control/areas"
                                    class="btn btn--primary-light btn--sm"
                                >
                                    Verwalten
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td
                                class="table__cell--strong"
                                data-label="Bereich"
                            >
                                Menüs
                                <span class="table__subtext">
                                    Menüs strukturieren die Navigation der Areas.
                                </span>
                            </td>

                            <td
                                class="table__cell--right"
                                data-label="Gesamt"
                            >
                                <?= htmlspecialchars((string) $menusTotal, ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td data-label="Status">
                                <?php if ($menusTotal > 0): ?>
                                    <span class="table-badge table-badge--success">
                                        Angelegt
                                    </span>
                                <?php else: ?>
                                    <span class="table-badge table-badge--warning">
                                        Leer
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td data-label="Aktion">
                                <a
                                    href="/development/web-control/menus"
                                    class="btn btn--primary-light btn--sm"
                                >
                                    Verwalten
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td
                                class="table__cell--strong"
                                data-label="Bereich"
                            >
                                Menu Items
                                <span class="table__subtext">
                                    Davon <?= htmlspecialchars((string) $menuItemsActive, ENT_QUOTES, 'UTF-8') ?> aktiv.
                                </span>
                            </td>

                            <td
                                class="table__cell--right"
                                data-label="Gesamt"
                            >
                                <?= htmlspecialchars((string) $menuItemsTotal, ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td data-label="Status">
                                <?php if ($menuItemsTotal > 0): ?>
                                    <span class="table-badge table-badge--success">
                                        Angelegt
                                    </span>
                                <?php else: ?>
                                    <span class="table-badge table-badge--neutral">
                                        Keine Items
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td data-label="Aktion">
                                <a
                                    href="/development/web-control/menus"
                                    class="btn btn--primary-light btn--sm"
                                >
                                    Zu Menüs
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">
                        Areas
                    </p>

                    <h2 class="table-block__title">
                        Aktuelle Bereiche
                    </h2>

                    <p class="table-block__subtitle">
                        Die zuletzt geladenen Portalbereiche aus der aktuellen Struktur.
                    </p>
                </div>

                <div class="table-block__actions btn-group btn-group--horizontal">
                    <a
                        href="/development/web-control/areas"
                        class="btn btn--primary-transp btn--md"
                    >
                        Alle Bereiche
                    </a>
                </div>
            </div>

            <?php if (empty($recentAreas)): ?>
                <div class="table-empty">
                    <h3 class="table-empty__title">
                        Keine Bereiche vorhanden
                    </h3>

                    <p class="table-empty__text">
                        Legen Sie den ersten Portalbereich an, um das Web-Control
                        mit Struktur zu füllen.
                    </p>

                    <div class="btn-group btn-group--horizontal btn-group--main-center">
                        <a
                            href="/development/web-control/areas/create"
                            class="btn btn--primary btn--md"
                        >
                            Bereich erstellen
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-wrapper table-wrapper--bordered">
                    <table class="table table--compact table--striped table--hover table--stack">
                        <caption>
                            Übersicht ausgewählter Portalbereiche.
                        </caption>

                        <thead>
                            <tr>
                                <th class="table__cell--min">
                                    ID
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Area Key
                                </th>

                                <th>
                                    Startpfad
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="table__cell--actions">
                                    Aktion
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($recentAreas as $area): ?>
                                <?php
                                $areaId = (string) ($area['id'] ?? '');
                                $areaName = (string) ($area['name'] ?? '');
                                $areaKey = (string) ($area['area_key'] ?? '');
                                $startPath = (string) ($area['start_path'] ?? '');
                                $isActive = !empty($area['is_active']);
                                $isExternal = !empty($area['is_external']);
                                ?>

                                <tr>
                                    <td
                                        class="table__cell--muted table__cell--min"
                                        data-label="ID"
                                    >
                                        <?= htmlspecialchars($areaId, ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--strong"
                                        data-label="Name"
                                    >
                                        <?= htmlspecialchars($areaName !== '' ? $areaName : '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--muted"
                                        data-label="Area Key"
                                    >
                                        <?= htmlspecialchars($areaKey !== '' ? $areaKey : '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--muted"
                                        data-label="Startpfad"
                                    >
                                        <?= htmlspecialchars($startPath !== '' ? $startPath : '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td data-label="Status">
                                        <?php if ($isActive): ?>
                                            <span class="table-badge table-badge--success">
                                                Aktiv
                                            </span>
                                        <?php else: ?>
                                            <span class="table-badge table-badge--neutral">
                                                Inaktiv
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($isExternal): ?>
                                            <span class="table-badge table-badge--info">
                                                Extern
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td
                                        class="table__cell--actions"
                                        data-label="Aktion"
                                    >
                                        <a
                                            href="/development/web-control/areas/edit?id=<?= urlencode($areaId) ?>"
                                            class="btn btn--primary-light btn--sm"
                                        >
                                            Bearbeiten
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section--surface">
    <div class="container container--text-only">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">
                        Navigation
                    </p>

                    <h2 class="table-block__title">
                        Aktuelle Menüs
                    </h2>

                    <p class="table-block__subtitle">
                        Übersicht der vorhandenen Menüs mit Anzahl der zugehörigen Items.
                    </p>
                </div>

                <div class="table-block__actions btn-group btn-group--horizontal">
                    <a
                        href="/development/web-control/menus"
                        class="btn btn--primary-transp btn--md"
                    >
                        Alle Menüs
                    </a>
                </div>
            </div>

            <?php if (empty($recentMenus)): ?>
                <div class="table-empty">
                    <h3 class="table-empty__title">
                        Keine Menüs vorhanden
                    </h3>

                    <p class="table-empty__text">
                        Legen Sie ein Menü an, um Navigationspunkte für Portalbereiche
                        zu erstellen.
                    </p>

                    <div class="btn-group btn-group--horizontal btn-group--main-center">
                        <a
                            href="/development/web-control/menus/create"
                            class="btn btn--primary btn--md"
                        >
                            Menü erstellen
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-wrapper table-wrapper--bordered">
                    <table class="table table--compact table--striped table--hover table--stack">
                        <caption>
                            Übersicht ausgewählter Menüs im Web-Control.
                        </caption>

                        <thead>
                            <tr>
                                <th class="table__cell--min">
                                    ID
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Slug
                                </th>

                                <th>
                                    Items
                                </th>

                                <th class="table__cell--actions">
                                    Aktion
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($recentMenus as $menu): ?>
                                <?php
                                $menuId = (string) ($menu['id'] ?? '');
                                $menuName = (string) ($menu['name'] ?? '');
                                $menuSlug = (string) ($menu['slug'] ?? '');
                                $itemsForMenu = $menuItemsByMenuId[$menuId] ?? [];
                                ?>

                                <tr>
                                    <td
                                        class="table__cell--muted table__cell--min"
                                        data-label="ID"
                                    >
                                        <?= htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--strong"
                                        data-label="Name"
                                    >
                                        <?= htmlspecialchars($menuName !== '' ? $menuName : '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--muted"
                                        data-label="Slug"
                                    >
                                        <?= htmlspecialchars($menuSlug !== '' ? $menuSlug : '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td data-label="Items">
                                        <?php if (!empty($itemsForMenu)): ?>
                                            <span class="table-badge table-badge--primary">
                                                <?= htmlspecialchars((string) count($itemsForMenu), ENT_QUOTES, 'UTF-8') ?>
                                                Items
                                            </span>
                                        <?php else: ?>
                                            <span class="table-badge table-badge--neutral">
                                                Keine Items
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td
                                        class="table__cell--actions"
                                        data-label="Aktion"
                                    >
                                        <a
                                            href="/development/web-control/menus/edit?id=<?= urlencode($menuId) ?>#menu-items"
                                            class="btn btn--primary-light btn--sm"
                                        >
                                            Bearbeiten
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>