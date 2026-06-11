<?php 

$area = $area ?? [];
$id = $area['id'] ?? '';
$areaKey = $area['area_key'] ?? '';
$icon = $area['icon'] ?? '';
$name = $area['name'] ?? '';
$description = $area['description'] ?? '';
$startPath = $area['start_path'] ?? '/';
$isActive = !empty($area['is_active']) ? 1 : 0;
$isExternal = !empty($area['is_external']) ? 1 : 0;
$sortOrder = $area['sort_order'] ?? 0;

$isEdit = $id !== '';
$action = $isEdit
    ? '/development/web-control/areas/edit'
    : '/development/web-control/areas/create';

$titleFallback = $isEdit
    ? 'Bereich bearbeiten'
    : 'Neuen Bereich erstellen';

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
                        <a href="/development/web-control/areas">
                            Bereiche
                        </a>
                    </li>

                    <li aria-current="page">
                        <?= htmlspecialchars($titleFallback, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                </ol>
            </nav>

            <p class="page-title__kicker">
                <?= $isEdit ? 'Portalbereich bearbeiten' : 'Portalbereich anlegen' ?>
            </p>

            <h1 class="page-title__title">
                <?= htmlspecialchars($currentTitle, ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <p class="page-title__lead">
                <?php if ($isEdit): ?>
                    Bearbeiten Sie die Stammdaten, Sichtbarkeit, Verlinkung und Sortierung
                    dieses Portalbereichs.
                <?php else: ?>
                    Legen Sie einen neuen Portalbereich an und definieren Sie Schlüssel,
                    Startpfad, Icon, Sichtbarkeit und Sortierung.
                <?php endif; ?>
            </p>

            <div class="page-title__badges" aria-label="Bereichsinformationen">
                <span class="page-title__badge">
                    Web-Control
                </span>

                <span class="page-title__badge">
                    Areas
                </span>

                <span class="page-title__badge">
                    <?= $isEdit ? 'Bearbeiten' : 'Erstellen' ?>
                </span>
            </div>
        </div>

        <div class="page-title__side">
            <div class="page-title__actions btn-group btn-group--horizontal">
                <a
                    href="/development/web-control/areas"
                    class="btn btn--primary-transp btn--md"
                >
                    Zur Bereichsübersicht
                </a>
            </div>
        </div>
    </header>
</section>

<section class="area-form">
    <form
        method="post"
        action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>"
        class="form form--card"
    >
        <div class="form__grid form__grid--2">
            <div class="form__title">
                <h2>
                    Bereichsdaten
                </h2>

                <p>
                    Tragen Sie die grundlegenden Informationen für den Portalbereich ein.
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
                    Grunddaten
                </h3>

                <p>
                    Name, Area Key und Icon bestimmen, wie der Bereich im Portal
                    technisch und redaktionell geführt wird.
                </p>
            </div>

            <div class="form__field form__field--span-2">
                <label class="form__label" for="area-name">
                    Name <span class="form__required">*</span>
                </label>

                <input
                    class="form__control"
                    id="area-name"
                    type="text"
                    name="name"
                    value="<?= htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="off"
                >

                <p class="form__hint">
                    Der Name wird als sichtbare Bezeichnung des Bereichs verwendet.
                </p>
            </div>

            <div class="form__field">
                <label class="form__label" for="area-key">
                    Area Key <span class="form__required">*</span>
                </label>

                <input
                    class="form__control"
                    id="area-key"
                    type="text"
                    name="area_key"
                    value="<?= htmlspecialchars((string) $areaKey, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="off"
                    placeholder="serviceportal"
                >

                <p class="form__hint">
                    Technischer Schlüssel des Bereichs. Möglichst kurz und eindeutig.
                </p>
            </div>

            <div class="form__field">
                <label class="form__label" for="area-icon">
                    Icon
                </label>

                <input
                    class="form__control"
                    id="area-icon"
                    type="text"
                    name="icon"
                    value="<?= htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="off"
                    placeholder="icon-home"
                >

                <p class="form__hint">
                    Optionaler Icon-Key, zum Beispiel aus der VDBS-Icon-Sammlung.
                </p>
            </div>

            <div class="form__section-title form__section-title--primary">
                <h3>
                    Verlinkung und Sortierung
                </h3>

                <p>
                    Der Startpfad steuert das Ziel des Bereichs. Die Sortierung
                    bestimmt die Reihenfolge in Übersichten und Navigationen.
                </p>
            </div>

            <div class="form__field">
                <label class="form__label" for="area-start-path">
                    Startpfad <span class="form__required">*</span>
                </label>

                <input
                    class="form__control"
                    id="area-start-path"
                    type="text"
                    name="start_path"
                    value="<?= htmlspecialchars((string) $startPath, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autocomplete="off"
                    placeholder="/"
                >

                <p class="form__hint">
                    Interner Pfad oder Zieladresse, auf die der Bereich verweist.
                </p>
            </div>

            <div class="form__field">
                <label class="form__label" for="area-sort-order">
                    Sortierung
                </label>

                <input
                    class="form__control"
                    id="area-sort-order"
                    type="number"
                    name="sort_order"
                    value="<?= htmlspecialchars((string) $sortOrder, ENT_QUOTES, 'UTF-8') ?>"
                    step="1"
                >

                <p class="form__hint">
                    Kleinere Zahlen erscheinen in der Regel weiter oben.
                </p>
            </div>

            <div class="form__field form__field--span-full">
                <label class="form__label" for="area-description">
                    Beschreibung
                </label>

                <textarea
                    class="form__control"
                    id="area-description"
                    name="description"
                    rows="6"
                    placeholder="Kurze Beschreibung des Bereichs..."
                ><?= htmlspecialchars((string) $description, ENT_QUOTES, 'UTF-8') ?></textarea>

                <p class="form__hint">
                    Die Beschreibung hilft bei der internen Einordnung des Portalbereichs.
                </p>
            </div>

            <div class="form__section-title form__section-title--secondary-highlight">
                <h3>
                    Sichtbarkeit und Verhalten
                </h3>

                <p>
                    Legen Sie fest, ob der Bereich aktiv ist und ob er auf ein externes
                    System verweist.
                </p>
            </div>

            <fieldset class="form__fieldset">
                <legend class="form__legend">
                    Bereichsoptionen
                </legend>

                <div class="form__choice-group">
                    <div class="form__check">
                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <input
                            class="form__check-input"
                            id="area-is-active"
                            type="checkbox"
                            name="is_active"
                            value="1"
                            <?= $isActive ? 'checked' : '' ?>
                        >

                        <label class="form__check-label" for="area-is-active">
                            <strong>Aktiv</strong>
                            <small>Der Bereich ist im Portal sichtbar.</small>
                        </label>
                    </div>

                    <div class="form__check">
                        <input
                            type="hidden"
                            name="is_external"
                            value="0"
                        >

                        <input
                            class="form__check-input"
                            id="area-is-external"
                            type="checkbox"
                            name="is_external"
                            value="1"
                            <?= $isExternal ? 'checked' : '' ?>
                        >

                        <label class="form__check-label" for="area-is-external">
                            <strong>Externes System</strong>
                            <small>Kennzeichnet Weiterleitungen oder Integrationen.</small>
                        </label>
                    </div>
                </div>
            </fieldset>

            <div class="form__actions form__actions--right">
                <a
                    class="btn btn--primary-transp btn--md"
                    href="/development/web-control/areas"
                >
                    Abbrechen
                </a>

                <button
                    class="btn btn--primary btn--md"
                    type="submit"
                >
                    Speichern
                </button>
            </div>
        </div>
    </form>
</section>