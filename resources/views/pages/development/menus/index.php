<?php declare(strict_types=1); ?>

<section class="section--page-title section--page-title-compact">
    <header class="page-title page-title--band page-title--secondary-cta page-title--split">
        <div class="page-title__main">
            <nav class="page-title__breadcrumb" aria-label="Breadcrumb">
                <ol class="page-title__breadcrumb-list">
                    <li>
                        <a href="/development/web-control">
                            Web-Control
                        </a>
                    </li>

                    <li aria-current="page">
                        Menüs
                    </li>
                </ol>
            </nav>

            <p class="page-title__kicker">
                Navigation verwalten
            </p>

            <h1 class="page-title__title">
                <?= htmlspecialchars($pageTitle ?? 'Menüverwaltung', ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <p class="page-title__lead">
                Verwalten Sie die Menüs des Portals, ihre zugeordneten Areas und
                die dazugehörigen Navigationspunkte.
            </p>

            <div class="page-title__badges" aria-label="Bereichsinformationen">
                <span class="page-title__badge">
                    Web-Control
                </span>

                <span class="page-title__badge">
                    Navigation
                </span>

                <span class="page-title__badge">
                    Menüs & Items
                </span>
            </div>
        </div>

        <div class="page-title__side">
            <div class="page-title__actions btn-group btn-group--horizontal">
                <a
                    href="/development/web-control/menus/create"
                    class="btn btn--primary btn--md"
                >
                    Neues Menü erstellen
                </a>
            </div>
        </div>
    </header>
</section>

<section class="menus-list">
    <?php if (empty($menus)): ?>
        <div class="table-empty">
            <h2 class="table-empty__title">
                Keine Menüs gefunden
            </h2>

            <p class="table-empty__text">
                Es wurden noch keine Menüs angelegt. Erstellen Sie ein neues Menü,
                um die Navigation des Portals zu strukturieren.
            </p>

            <div class="btn-group btn-group--horizontal btn-group--main-center">
                <a
                    href="/development/web-control/menus/create"
                    class="btn btn--primary btn--md"
                >
                    Neues Menü erstellen
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">
                        Übersicht
                    </p>

                    <h2 class="table-block__title">
                        Angelegte Menüs
                    </h2>

                    <p class="table-block__subtitle">
                        Übersicht aller Menüs mit Area, Name, Slug, Anzahl der Items
                        und direkten Bearbeitungsoptionen.
                    </p>
                </div>

                <div class="table-block__actions btn-group btn-group--horizontal">
                    <a
                        href="/development/web-control/menus/create"
                        class="btn btn--primary btn--md"
                    >
                        Neu erstellen
                    </a>
                </div>
            </div>

            <div class="table-wrapper table-wrapper--bordered">
                <table class="table table--compact table--striped table--hover table--stack">
                    <caption>
                        Liste aller aktuell angelegten Menüs im Web-Control-Bereich.
                    </caption>

                    <thead>
                        <tr>
                            <th class="table__cell--min">
                                ID
                            </th>

                            <th>
                                Area
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
                                Aktionen
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($menus as $menu): ?>
                            <?php
                            $menuId = (string) ($menu['id'] ?? '');
                            $itemsForMenu = $menuItemsByMenuId[$menuId] ?? $menuItemsByMenuId[(int) $menuId] ?? [];
                            $itemCount = $menuItemCounts[$menuId] ?? $menuItemCounts[(int) $menuId] ?? count($itemsForMenu);
                            ?>

                            <tr>
                                <td
                                    class="table__cell--muted table__cell--min"
                                    data-label="ID"
                                >
                                    <?= htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td data-label="Area">
                                    <?= htmlspecialchars((string) ($areasMap[$menu['area_id']] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--strong"
                                    data-label="Name"
                                >
                                    <?= htmlspecialchars((string) ($menu['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--muted table__cell--nowrap"
                                    data-label="Slug"
                                >
                                    <?= htmlspecialchars((string) ($menu['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td data-label="Items">
                                    <?php if ((int) $itemCount > 0): ?>
                                        <span class="table-badge table-badge--primary">
                                            <?= htmlspecialchars((string) $itemCount, ENT_QUOTES, 'UTF-8') ?>
                                            Items
                                        </span>

                                        <?php if (!empty($itemsForMenu)): ?>
                                            <span class="table__subtext">
                                                <?php
                                                $itemTitles = array_map(
                                                    static fn (array $item): string => (string) ($item['title'] ?? ''),
                                                    array_slice($itemsForMenu, 0, 3)
                                                );

                                                echo htmlspecialchars(
                                                    implode(', ', array_filter($itemTitles)),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>

                                                <?php if ((int) $itemCount > 3): ?>
                                                    …
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="table-badge table-badge--neutral">
                                            Keine Items
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td
                                    class="table__cell--actions"
                                    data-label="Aktionen"
                                >
                                    <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                                        <a
                                            class="btn btn--primary-light btn--sm"
                                            href="/development/web-control/menus/edit?id=<?= urlencode($menuId) ?>#menu-items"
                                        >
                                            Items
                                        </a>

                                        <a
                                            class="btn btn--primary-light btn--sm"
                                            href="/development/web-control/menus/edit?id=<?= urlencode($menuId) ?>"
                                        >
                                            Bearbeiten
                                        </a>

                                        <form
                                            method="post"
                                            action="/development/web-control/menus/delete"
                                        >
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= htmlspecialchars($menuId, ENT_QUOTES, 'UTF-8') ?>"
                                            >

                                            <button
                                                class="btn btn--danger btn--sm"
                                                type="submit"
                                            >
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>