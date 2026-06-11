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
                        Bereiche
                    </li>
                </ol>
            </nav>

            <p class="page-title__kicker">
                Portalbereiche verwalten
            </p>

            <h1 class="page-title__title">
                <?= htmlspecialchars($pageTitle ?? 'Bereiche', ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <p class="page-title__lead">
                Verwalten Sie die zentralen Bereiche des Portals, ihre Schlüssel,
                Icons, Startpfade, Sichtbarkeit und Sortierung.
            </p>

            <div class="page-title__badges" aria-label="Bereichsinformationen">
                <span class="page-title__badge">
                    Web-Control
                </span>

                <span class="page-title__badge">
                    Areas
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
                    Neuen Bereich erstellen
                </a>
            </div>
        </div>
    </header>
</section>

<section class="areas-list">
    <?php if (empty($areas)): ?>
        <div class="table-empty">
            <h2 class="table-empty__title">
                Keine Bereiche gefunden
            </h2>

            <p class="table-empty__text">
                Es wurden noch keine Portalbereiche angelegt. Erstellen Sie einen
                neuen Bereich, um die Portalstruktur aufzubauen.
            </p>

            <div class="btn-group btn-group--horizontal btn-group--main-center">
                <a
                    href="/development/web-control/areas/create"
                    class="btn btn--primary btn--md"
                >
                    Neuen Bereich erstellen
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
                        Angelegte Bereiche
                    </h2>

                    <p class="table-block__subtitle">
                        Übersicht aller Portalbereiche mit Area Key, Icon, Name,
                        Startpfad, Status und Sortierung.
                    </p>
                </div>

                <div class="table-block__actions btn-group btn-group--horizontal">
                    <a
                        href="/development/web-control/areas/create"
                        class="btn btn--primary btn--md"
                    >
                        Neu erstellen
                    </a>
                </div>
            </div>

            <div class="table-wrapper table-wrapper--bordered">
                <table class="table table--compact table--striped table--hover table--stack">
                    <caption>
                        Liste aller aktuell angelegten Portalbereiche im Web-Control-Bereich.
                    </caption>

                    <thead>
                        <tr>
                            <th class="table__cell--min">
                                ID
                            </th>

                            <th>
                                Area Key
                            </th>

                            <th>
                                Icon
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Startpfad
                            </th>

                            <th>
                                Aktiv
                            </th>

                            <th>
                                Extern
                            </th>

                            <th class="table__cell--right">
                                Sortierung
                            </th>

                            <th class="table__cell--actions">
                                Aktionen
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($areas as $area): ?>
                            <tr>
                                <td
                                    class="table__cell--muted table__cell--min"
                                    data-label="ID"
                                >
                                    <?= htmlspecialchars((string) ($area['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--strong table__cell--nowrap"
                                    data-label="Area Key"
                                >
                                    <?= htmlspecialchars((string) ($area['area_key'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--muted table__cell--nowrap"
                                    data-label="Icon"
                                >
                                    <?= htmlspecialchars((string) ($area['icon'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--strong"
                                    data-label="Name"
                                >
                                    <?= htmlspecialchars((string) ($area['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--muted table__cell--nowrap"
                                    data-label="Startpfad"
                                >
                                    <?= htmlspecialchars((string) ($area['start_path'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td data-label="Aktiv">
                                    <?php if (!empty($area['is_active'])): ?>
                                        <span class="table-badge table-badge--success">
                                            Aktiv
                                        </span>
                                    <?php else: ?>
                                        <span class="table-badge table-badge--neutral">
                                            Inaktiv
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Extern">
                                    <?php if (!empty($area['is_external'])): ?>
                                        <span class="table-badge table-badge--info">
                                            Extern
                                        </span>
                                    <?php else: ?>
                                        <span class="table-badge table-badge--neutral">
                                            Intern
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td
                                    class="table__cell--right table__cell--nowrap"
                                    data-label="Sortierung"
                                >
                                    <?= htmlspecialchars((string) ($area['sort_order'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>

                                <td
                                    class="table__cell--actions"
                                    data-label="Aktionen"
                                >
                                    <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                                        <a
                                            class="btn btn--primary-light btn--sm"
                                            href="/development/web-control/areas/edit?id=<?= urlencode((string) ($area['id'] ?? '')) ?>"
                                        >
                                            Bearbeiten
                                        </a>

                                        <form
                                            method="post"
                                            action="/development/web-control/areas/delete"
                                        >
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= htmlspecialchars((string) ($area['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
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