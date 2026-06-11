<?php declare(strict_types=1);

$menu = $menu ?? [];
$id = $menu['id'] ?? '';
$name = $menu['name'] ?? '';
$slug = $menu['slug'] ?? '';
$areaId = $menu['area_id'] ?? '';
$areas = $areas ?? [];
$areaLabel = $areaLabel ?? '';
$menuItems = $menuItems ?? [];

$menuItemsByParentId = [];
$menuItemsTreeRows = [];
$visitedMenuItemIds = [];

foreach ($menuItems as $menuItem) {
    $itemId = (string) ($menuItem['id'] ?? '');

    if ($itemId === '') {
        continue;
    }

    $parentIdRaw = $menuItem['parent_id'] ?? '';
    $parentId = (string) $parentIdRaw;

    if ($parentIdRaw === null || $parentId === '' || $parentId === '0') {
        $parentId = '';
    }

    $menuItemsByParentId[$parentId][] = $menuItem;
}

$sortMenuItems = static function (array $items): array {
    usort(
        $items,
        static function (array $a, array $b): int {
            $orderA = (int) ($a['order_index'] ?? 0);
            $orderB = (int) ($b['order_index'] ?? 0);

            if ($orderA === $orderB) {
                return (int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0);
            }

            return $orderA <=> $orderB;
        }
    );

    return $items;
};

$buildMenuItemRows = function (string $parentId, int $depth) use (
    &$buildMenuItemRows,
    &$menuItemsTreeRows,
    &$visitedMenuItemIds,
    $menuItemsByParentId,
    $sortMenuItems
): void {
    $children = $menuItemsByParentId[$parentId] ?? [];

    foreach ($sortMenuItems($children) as $child) {
        $childId = (string) ($child['id'] ?? '');

        if ($childId === '' || isset($visitedMenuItemIds[$childId])) {
            continue;
        }

        $visitedMenuItemIds[$childId] = true;
        $child['_depth'] = $depth;

        $menuItemsTreeRows[] = $child;

        $buildMenuItemRows($childId, $depth + 1);
    }
};

$buildMenuItemRows('', 0);

/*
 * Sicherheitsnetz:
 * Falls es verwaiste Items gibt, deren parent_id nicht mehr existiert,
 * werden sie trotzdem am Ende der Tabelle angezeigt.
 */
foreach ($menuItems as $menuItem) {
    $itemId = (string) ($menuItem['id'] ?? '');

    if ($itemId === '' || isset($visitedMenuItemIds[$itemId])) {
        continue;
    }

    $visitedMenuItemIds[$itemId] = true;
    $menuItem['_depth'] = 0;
    $menuItem['_is_orphan'] = true;

    $menuItemsTreeRows[] = $menuItem;
}

$isEdit = $id !== '';
$action = $isEdit
    ? '/development/web-control/menus/edit'
    : '/development/web-control/menus/create';

$menuItemCreateAction = $menuItemCreateAction ?? '/development/web-control/menu-items/create';
$menuItemEditAction = $menuItemEditAction ?? '/development/web-control/menu-items/edit';
$menuItemDeleteAction = $menuItemDeleteAction ?? '/development/web-control/menu-items/delete';

$titleFallback = $isEdit
    ? 'Menü bearbeiten'
    : 'Neues Menü erstellen';

$currentTitle = $pageTitle ?? $titleFallback;
?>

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

                    <li>
                        <a href="/development/web-control/menus">
                            Menüs
                        </a>
                    </li>

                    <li aria-current="page">
                        <?= htmlspecialchars($titleFallback, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                </ol>
            </nav>

            <p class="page-title__kicker">
                <?= $isEdit ? 'Navigation bearbeiten' : 'Navigation anlegen' ?>
            </p>

            <h1 class="page-title__title">
                <?= htmlspecialchars($currentTitle, ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <p class="page-title__lead">
                <?php if ($isEdit): ?>
                    Bearbeiten Sie die Menüdaten und verwalten Sie die zugehörigen
                    Navigationspunkte übersichtlich in einer Tabelle.
                <?php else: ?>
                    Legen Sie ein neues Menü an. Die zugehörigen Items können nach
                    dem ersten Speichern ergänzt werden.
                <?php endif; ?>
            </p>

            <div class="page-title__badges" aria-label="Bereichsinformationen">
                <span class="page-title__badge">
                    Web-Control
                </span>

                <span class="page-title__badge">
                    Navigation
                </span>

                <span class="page-title__badge">
                    <?= $isEdit ? 'Menü & Items' : 'Menü erstellen' ?>
                </span>
            </div>
        </div>

        <div class="page-title__side">
            <div class="page-title__actions btn-group btn-group--horizontal">
                <a
                    href="/development/web-control/menus"
                    class="btn btn--primary-transp btn--md"
                >
                    Zur Menüübersicht
                </a>
            </div>
        </div>
    </header>
</section>

<section class="menu-form">
    <form
        method="post"
        action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>"
        class="form form--card"
    >
        <div class="form__grid form__grid--2">
            <div class="form__title">
                <h2>
                    Menüdaten
                </h2>

                <p>
                    Tragen Sie die grundlegenden Informationen für das Menü ein.
                    Felder mit <span class="form__required">*</span> sind erforderlich.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="form__notice form__notice--error" role="alert">
                    <strong>Bitte prüfen Sie Ihre Eingaben.</strong>

                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li>
                                <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($id !== ''): ?>
                <input
                    type="hidden"
                    name="id"
                    value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>"
                >
            <?php endif; ?>

            <div class="form__section-title form__section-title--secondary-cta">
                <h3>
                    Zuordnung
                </h3>

                <p>
                    Die Area bestimmt, in welchem Portalbereich das Menü verwendet wird.
                </p>
            </div>

            <?php if ($isEdit): ?>
                <div class="form__field form__field--span-full">
                    <label class="form__label" for="menu-area-label">
                        Area
                    </label>

                    <input
                        class="form__control"
                        id="menu-area-label"
                        type="text"
                        value="<?= htmlspecialchars((string) ($areaLabel !== '' ? $areaLabel : 'Unbekannt'), ENT_QUOTES, 'UTF-8') ?>"
                        readonly
                    >

                    <p class="form__hint">
                        Die Area kann bei bestehenden Menüs nicht nachträglich geändert werden.
                    </p>
                </div>
            <?php else: ?>
                <?php if (empty($areas)): ?>
                    <div class="form__notice form__notice--warning">
                        <strong>Keine freie Area verfügbar:</strong>
                        Alle Areas haben bereits ein Menü. Ein neues Menü kann erst
                        angelegt werden, wenn eine freie Area verfügbar ist.
                    </div>
                <?php endif; ?>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="menu-area">
                        Area <span class="form__required">*</span>
                    </label>

                    <select
                        class="form__control"
                        id="menu-area"
                        name="area_id"
                        required
                        <?= empty($areas) ? 'disabled' : '' ?>
                    >
                        <option value="">
                            — Bitte wählen —
                        </option>

                        <?php foreach ($areas as $a): ?>
                            <option
                                value="<?= htmlspecialchars((string) $a['id'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= ((string) $a['id'] === (string) $areaId) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars((string) $a['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <p class="form__hint">
                        Wählen Sie die Area aus, für die dieses Menü gelten soll.
                    </p>
                </div>
            <?php endif; ?>

            <div class="form__section-title form__section-title--primary">
                <h3>
                    Menüinformationen
                </h3>

                <p>
                    Name und Slug werden zur Anzeige und technischen Zuordnung des Menüs verwendet.
                </p>
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="menu-name">
                    Name <span class="form__required">*</span>
                </label>

                <input
                    class="form__control"
                    id="menu-name"
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="off"
                >

                <p class="form__hint">
                    Der Name sollte kurz und eindeutig sein, zum Beispiel „Hauptnavigation“.
                </p>
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="menu-slug">
                    Slug <span class="form__required">*</span>
                </label>

                <input
                    class="form__control"
                    id="menu-slug"
                    type="text"
                    name="slug"
                    value="<?= htmlspecialchars((string) $slug, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="off"
                    placeholder="hauptnavigation"
                >

                <p class="form__hint">
                    Der Slug dient als technische Kennung und sollte kleingeschrieben sein.
                </p>
            </div>

            <div class="form__actions form__actions--right">
                <a
                    class="btn btn--primary-transp btn--md"
                    href="/development/web-control/menus"
                >
                    Abbrechen
                </a>

                <button
                    class="btn btn--primary btn--md"
                    type="submit"
                    <?= (!$isEdit && empty($areas)) ? 'disabled' : '' ?>
                >
                    Menüdaten speichern
                </button>
            </div>
        </div>
    </form>
</section>

<?php if ($isEdit): ?>
    <section class="menu-items-list" id="menu-items">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <div>
                    <p class="table-block__kicker">
                        Navigationspunkte
                    </p>

                    <h2 class="table-block__title">
                        Menu Items
                    </h2>

                    <p class="table-block__subtitle">
                        Verwalten Sie die einzelnen Navigationspunkte dieses Menüs.
                        Die Einrückung zeigt die Ebene innerhalb der Navigation.
                    </p>
                </div>

                <div class="table-block__actions btn-group btn-group--horizontal">
                    <button
                        class="btn btn--primary btn--md"
                        type="button"
                        data-dialog-open="menu-item-create-dialog"
                    >
                        Neues Item erstellen
                    </button>
                </div>
            </div>

            <?php if (empty($menuItemsTreeRows)): ?>
                <div class="table-empty">
                    <h3 class="table-empty__title">
                        Noch keine Items vorhanden
                    </h3>

                    <p class="table-empty__text">
                        Dieses Menü enthält bisher keine Navigationspunkte.
                        Erstellen Sie ein neues Item, um die Navigation aufzubauen.
                    </p>

                    <div class="btn-group btn-group--horizontal btn-group--main-center">
                        <button
                            class="btn btn--primary btn--md"
                            type="button"
                            data-dialog-open="menu-item-create-dialog"
                        >
                            Neues Item erstellen
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-wrapper table-wrapper--bordered">
                    <table class="table table--compact table--striped table--hover table--stack">
                        <caption>
                            Übersicht aller Navigationspunkte dieses Menüs mit sichtbarer Ebenenstruktur.
                        </caption>

                        <thead>
                            <tr>
                                <th class="table__cell--min">
                                    ID
                                </th>

                                <th>
                                    Struktur / Titel
                                </th>

                                <th>
                                    Slug
                                </th>

                                <th>
                                    URL / Route
                                </th>

                                <th>
                                    Parent
                                </th>

                                <th>
                                    Ebene
                                </th>

                                <th class="table__cell--right">
                                    Sortierung
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="table__cell--actions">
                                    Aktionen
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($menuItemsTreeRows as $item): ?>
                                <?php
                                $itemId = (string) ($item['id'] ?? '');
                                $itemParentId = (string) ($item['parent_id'] ?? '');
                                $itemTitle = (string) ($item['title'] ?? '');
                                $itemSlug = (string) ($item['slug'] ?? '');
                                $itemUrl = (string) ($item['url'] ?? '');
                                $itemRouteName = (string) ($item['route_name'] ?? '');
                                $itemTarget = (string) ($item['target'] ?? '');
                                $itemOrderIndex = (string) ($item['order_index'] ?? '');
                                $itemLevel = (string) ($item['level'] ?? '1');
                                $itemDepth = (int) ($item['_depth'] ?? 0);
                                $visualLevel = $itemDepth + 1;
                                $itemIsOrphan = !empty($item['_is_orphan']);
                                $itemIsActive = !array_key_exists('is_active', $item) || !empty($item['is_active']);
                                $indent = str_repeat('— ', $itemDepth);

                                $parentLabel = '—';

                                foreach ($menuItems as $possibleParent) {
                                    if ((string) ($possibleParent['id'] ?? '') === $itemParentId) {
                                        $parentLabel = (string) ($possibleParent['title'] ?? '—');
                                        break;
                                    }
                                }
                                ?>

                                <tr>
                                    <td
                                        class="table__cell--muted table__cell--min"
                                        data-label="ID"
                                    >
                                        <?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--strong"
                                        data-label="Struktur / Titel"
                                    >
                                        <span
                                            style="display: inline-block; padding-left: <?= htmlspecialchars((string) ($itemDepth * 1.25), ENT_QUOTES, 'UTF-8') ?>rem;"
                                        >
                                            <?php if ($itemDepth > 0): ?>
                                                <span class="table__cell--muted" aria-hidden="true">
                                                    <?= htmlspecialchars($indent, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            <?php endif; ?>

                                            <?= htmlspecialchars($itemTitle !== '' ? $itemTitle : '—', ENT_QUOTES, 'UTF-8') ?>
                                        </span>

                                        <span class="table__subtext">
                                            <?php if ($itemIsOrphan): ?>
                                                <span class="table-badge table-badge--warning">
                                                    Parent fehlt
                                                </span>
                                            <?php elseif ($parentLabel !== '—'): ?>
                                                Parent: <?= htmlspecialchars($parentLabel, ENT_QUOTES, 'UTF-8') ?>
                                            <?php else: ?>
                                                Hauptebene
                                            <?php endif; ?>
                                        </span>

                                        <?php if ($itemTarget === '_blank'): ?>
                                            <span class="table__subtext">
                                                Öffnet in neuem Fenster
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td
                                        class="table__cell--muted table__cell--nowrap"
                                        data-label="Slug"
                                    >
                                        <?= htmlspecialchars($itemSlug !== '' ? $itemSlug : '—', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td
                                        class="table__cell--muted"
                                        data-label="URL / Route"
                                    >
                                        <?php if ($itemUrl !== ''): ?>
                                            <?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>
                                        <?php elseif ($itemRouteName !== ''): ?>
                                            <?= htmlspecialchars($itemRouteName, ENT_QUOTES, 'UTF-8') ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>

                                        <?php if ($itemUrl !== '' && $itemRouteName !== ''): ?>
                                            <span class="table__subtext">
                                                Route: <?= htmlspecialchars($itemRouteName, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td data-label="Parent">
                                        <?= htmlspecialchars($parentLabel, ENT_QUOTES, 'UTF-8') ?>

                                        <?php if ($itemIsOrphan && $itemParentId !== ''): ?>
                                            <span class="table__subtext">
                                                gespeicherte Parent-ID:
                                                <?= htmlspecialchars($itemParentId, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td data-label="Ebene">
                                        <span class="table-badge table-badge--primary">
                                            Ebene <?= htmlspecialchars((string) $visualLevel, ENT_QUOTES, 'UTF-8') ?>
                                        </span>

                                        <?php if ($itemLevel !== (string) $visualLevel): ?>
                                            <span class="table__subtext">
                                                gespeichert:
                                                <?= htmlspecialchars($itemLevel, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td
                                        class="table__cell--right"
                                        data-label="Sortierung"
                                    >
                                        <?= htmlspecialchars($itemOrderIndex !== '' ? $itemOrderIndex : '0', ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td data-label="Status">
                                        <?php if ($itemIsActive): ?>
                                            <span class="table-badge table-badge--success">
                                                Aktiv
                                            </span>
                                        <?php else: ?>
                                            <span class="table-badge table-badge--neutral">
                                                Inaktiv
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td
                                        class="table__cell--actions"
                                        data-label="Aktionen"
                                    >
                                        <div class="btn-group btn-group--horizontal btn-group--gap-sm">
                                            <button
                                                class="btn btn--primary-light btn--sm"
                                                type="button"
                                                data-dialog-open="menu-item-edit-dialog-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                                Bearbeiten
                                            </button>

                                            <form
                                                method="post"
                                                action="<?= htmlspecialchars($menuItemDeleteAction, ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="menu_id"
                                                    value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
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
            <?php endif; ?>
        </div>
    </section>

    <dialog
        class="popup popup--modal popup--lg popup--secondary-cta"
        id="menu-item-create-dialog"
        aria-labelledby="menu-item-create-title"
    >
        <div class="popup__surface">
            <header class="popup__header">
                <div>
                    <p class="popup__kicker">
                        Menü-Item
                    </p>

                    <h2 class="popup__title" id="menu-item-create-title">
                        Neues Item erstellen
                    </h2>

                    <p class="popup__meta">
                        Ergänzen Sie einen neuen Navigationspunkt für dieses Menü.
                    </p>
                </div>

                <form method="dialog">
                    <button
                        class="popup__close"
                        type="submit"
                        aria-label="Dialog schließen"
                    >
                        ×
                    </button>
                </form>
            </header>

            <form
                method="post"
                action="<?= htmlspecialchars($menuItemCreateAction, ENT_QUOTES, 'UTF-8') ?>"
            >
                <div class="popup__body">
                    <input
                        type="hidden"
                        name="menu_id"
                        value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div class="form">
                        <div class="form__grid form__grid--4">
                            <div class="form__field form__field--span-2">
                                <label class="form__label" for="new-item-title">
                                    Titel <span class="form__required">*</span>
                                </label>

                                <input
                                    class="form__control"
                                    id="new-item-title"
                                    type="text"
                                    name="title"
                                    required
                                    autocomplete="off"
                                >
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="new-item-slug">
                                    Slug
                                </label>

                                <input
                                    class="form__control"
                                    id="new-item-slug"
                                    type="text"
                                    name="slug"
                                    autocomplete="off"
                                >
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="new-item-order-index">
                                    Sortierung
                                </label>

                                <input
                                    class="form__control"
                                    id="new-item-order-index"
                                    type="number"
                                    name="order_index"
                                    value="0"
                                    step="1"
                                >
                            </div>

                            <div class="form__field form__field--span-2">
                                <label class="form__label" for="new-item-url">
                                    URL
                                </label>

                                <input
                                    class="form__control"
                                    id="new-item-url"
                                    type="text"
                                    name="url"
                                    autocomplete="off"
                                    placeholder="/mein-pfad"
                                >
                            </div>

                            <div class="form__field form__field--span-2">
                                <label class="form__label" for="new-item-route-name">
                                    Route Name
                                </label>

                                <input
                                    class="form__control"
                                    id="new-item-route-name"
                                    type="text"
                                    name="route_name"
                                    autocomplete="off"
                                >
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="new-item-parent-id">
                                    Parent
                                </label>

                                <select
                                    class="form__control"
                                    id="new-item-parent-id"
                                    name="parent_id"
                                >
                                    <option value="">
                                        — Kein Parent —
                                    </option>

                                    <?php foreach ($menuItems as $parent): ?>
                                        <?php
                                        $parentId = (string) ($parent['id'] ?? '');
                                        $parentTitle = (string) ($parent['title'] ?? '');
                                        ?>

                                        <?php if ($parentId !== ''): ?>
                                            <option value="<?= htmlspecialchars($parentId, ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($parentTitle, ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="new-item-level">
                                    Ebene
                                </label>

                                <input
                                    class="form__control"
                                    id="new-item-level"
                                    type="number"
                                    name="level"
                                    value="1"
                                    min="1"
                                    max="3"
                                    step="1"
                                >
                            </div>

                            <div class="form__field">
                                <label class="form__label" for="new-item-target">
                                    Target
                                </label>

                                <select
                                    class="form__control"
                                    id="new-item-target"
                                    name="target"
                                >
                                    <option value="">
                                        Gleiches Fenster
                                    </option>

                                    <option value="_blank">
                                        Neues Fenster
                                    </option>
                                </select>
                            </div>

                            <div class="form__field">
                                <div class="form__check">
                                    <input
                                        type="hidden"
                                        name="is_active"
                                        value="0"
                                    >

                                    <input
                                        class="form__check-input"
                                        id="new-item-is-active"
                                        type="checkbox"
                                        name="is_active"
                                        value="1"
                                        checked
                                    >

                                    <label class="form__check-label" for="new-item-is-active">
                                        Aktiv
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="popup__footer">
                    <div class="btn-group btn-group--horizontal btn-group--main-end">
                        <button
                            class="btn btn--primary-transp btn--md"
                            type="button"
                            data-dialog-close
                        >
                            Abbrechen
                        </button>

                        <button
                            class="btn btn--primary btn--md"
                            type="submit"
                        >
                            Item erstellen
                        </button>
                    </div>
                </footer>
            </form>
        </div>
    </dialog>

    <?php foreach ($menuItems as $item): ?>
        <?php
        $itemId = (string) ($item['id'] ?? '');
        $itemParentId = (string) ($item['parent_id'] ?? '');
        $itemTitle = (string) ($item['title'] ?? '');
        $itemSlug = (string) ($item['slug'] ?? '');
        $itemUrl = (string) ($item['url'] ?? '');
        $itemRouteName = (string) ($item['route_name'] ?? '');
        $itemTarget = (string) ($item['target'] ?? '');
        $itemOrderIndex = (string) ($item['order_index'] ?? '');
        $itemLevel = (string) ($item['level'] ?? '1');
        $itemIsActive = !array_key_exists('is_active', $item) || !empty($item['is_active']);
        ?>

        <dialog
            class="popup popup--modal popup--lg popup--secondary-cta"
            id="menu-item-edit-dialog-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
            aria-labelledby="menu-item-edit-title-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
        >
            <div class="popup__surface">
                <header class="popup__header">
                    <div>
                        <p class="popup__kicker">
                            Menü-Item
                        </p>

                        <h2
                            class="popup__title"
                            id="menu-item-edit-title-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                        >
                            Item bearbeiten
                        </h2>

                        <p class="popup__meta">
                            <?= htmlspecialchars($itemTitle !== '' ? $itemTitle : 'Navigationspunkt bearbeiten', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <form method="dialog">
                        <button
                            class="popup__close"
                            type="submit"
                            aria-label="Dialog schließen"
                        >
                            ×
                        </button>
                    </form>
                </header>

                <form
                    method="post"
                    action="<?= htmlspecialchars($menuItemEditAction, ENT_QUOTES, 'UTF-8') ?>"
                >
                    <div class="popup__body">
                        <input
                            type="hidden"
                            name="menu_id"
                            value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <div class="form">
                            <div class="form__grid form__grid--4">
                                <div class="form__field form__field--span-2">
                                    <label
                                        class="form__label"
                                        for="edit-item-title-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Titel <span class="form__required">*</span>
                                    </label>

                                    <input
                                        class="form__control"
                                        id="edit-item-title-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        type="text"
                                        name="title"
                                        value="<?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8') ?>"
                                        required
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="form__field">
                                    <label
                                        class="form__label"
                                        for="edit-item-slug-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Slug
                                    </label>

                                    <input
                                        class="form__control"
                                        id="edit-item-slug-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        type="text"
                                        name="slug"
                                        value="<?= htmlspecialchars($itemSlug, ENT_QUOTES, 'UTF-8') ?>"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="form__field">
                                    <label
                                        class="form__label"
                                        for="edit-item-order-index-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Sortierung
                                    </label>

                                    <input
                                        class="form__control"
                                        id="edit-item-order-index-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        type="number"
                                        name="order_index"
                                        value="<?= htmlspecialchars($itemOrderIndex !== '' ? $itemOrderIndex : '0', ENT_QUOTES, 'UTF-8') ?>"
                                        step="1"
                                    >
                                </div>

                                <div class="form__field form__field--span-2">
                                    <label
                                        class="form__label"
                                        for="edit-item-url-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        URL
                                    </label>

                                    <input
                                        class="form__control"
                                        id="edit-item-url-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        type="text"
                                        name="url"
                                        value="<?= htmlspecialchars($itemUrl, ENT_QUOTES, 'UTF-8') ?>"
                                        autocomplete="off"
                                        placeholder="/mein-pfad"
                                    >
                                </div>

                                <div class="form__field form__field--span-2">
                                    <label
                                        class="form__label"
                                        for="edit-item-route-name-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Route Name
                                    </label>

                                    <input
                                        class="form__control"
                                        id="edit-item-route-name-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        type="text"
                                        name="route_name"
                                        value="<?= htmlspecialchars($itemRouteName, ENT_QUOTES, 'UTF-8') ?>"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="form__field">
                                    <label
                                        class="form__label"
                                        for="edit-item-parent-id-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Parent
                                    </label>

                                    <select
                                        class="form__control"
                                        id="edit-item-parent-id-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        name="parent_id"
                                    >
                                        <option value="">
                                            — Kein Parent —
                                        </option>

                                        <?php foreach ($menuItems as $parent): ?>
                                            <?php
                                            $parentId = (string) ($parent['id'] ?? '');
                                            $parentTitle = (string) ($parent['title'] ?? '');

                                            if ($parentId === '' || $parentId === $itemId) {
                                                continue;
                                            }
                                            ?>

                                            <option
                                                value="<?= htmlspecialchars($parentId, ENT_QUOTES, 'UTF-8') ?>"
                                                <?= $parentId === $itemParentId ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($parentTitle, ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form__field">
                                    <label
                                        class="form__label"
                                        for="edit-item-level-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Ebene
                                    </label>

                                    <input
                                        class="form__control"
                                        id="edit-item-level-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        type="number"
                                        name="level"
                                        value="<?= htmlspecialchars($itemLevel, ENT_QUOTES, 'UTF-8') ?>"
                                        min="1"
                                        max="3"
                                        step="1"
                                    >
                                </div>

                                <div class="form__field">
                                    <label
                                        class="form__label"
                                        for="edit-item-target-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        Target
                                    </label>

                                    <select
                                        class="form__control"
                                        id="edit-item-target-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        name="target"
                                    >
                                        <option value="" <?= $itemTarget === '' ? 'selected' : '' ?>>
                                            Gleiches Fenster
                                        </option>

                                        <option value="_blank" <?= $itemTarget === '_blank' ? 'selected' : '' ?>>
                                            Neues Fenster
                                        </option>
                                    </select>
                                </div>

                                <div class="form__field">
                                    <div class="form__check">
                                        <input
                                            type="hidden"
                                            name="is_active"
                                            value="0"
                                        >

                                        <input
                                            class="form__check-input"
                                            id="edit-item-is-active-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            <?= $itemIsActive ? 'checked' : '' ?>
                                        >

                                        <label
                                            class="form__check-label"
                                            for="edit-item-is-active-<?= htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                            Aktiv
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <footer class="popup__footer">
                        <div class="btn-group btn-group--horizontal btn-group--main-end">
                            <button
                                class="btn btn--primary-transp btn--md"
                                type="button"
                                data-dialog-close
                            >
                                Abbrechen
                            </button>

                            <button
                                class="btn btn--primary btn--md"
                                type="submit"
                            >
                                Änderungen speichern
                            </button>
                        </div>
                    </footer>
                </form>
            </div>
        </dialog>
    <?php endforeach; ?>

    <script>
        document.addEventListener('click', function (event) {
            const openButton = event.target.closest('[data-dialog-open]');
            const closeButton = event.target.closest('[data-dialog-close]');

            if (openButton) {
                const dialogId = openButton.getAttribute('data-dialog-open');
                const dialog = document.getElementById(dialogId);

                if (dialog && typeof dialog.showModal === 'function') {
                    dialog.showModal();
                }

                return;
            }

            if (closeButton) {
                const dialog = closeButton.closest('dialog');

                if (dialog && typeof dialog.close === 'function') {
                    dialog.close();
                }
            }
        });

        document.addEventListener('click', function (event) {
            const dialog = event.target.closest('dialog.popup');

            if (!dialog || event.target !== dialog) {
                return;
            }

            dialog.close();
        });
    </script>
<?php endif; ?>