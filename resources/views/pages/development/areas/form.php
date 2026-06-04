<?php declare(strict_types=1); ?>
<section class="area-form">
    <h1><?= htmlspecialchars($pageTitle ?? 'Bereich', ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

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
    $action = $id === '' ? '/development/web-control/areas/create' : '/development/web-control/areas/edit';
    ?>

    <form method="post" action="<?= $action ?>" class="form">
        <?php if ($id !== ''): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>" />
        <?php endif; ?>
        <div class="form__field">
            <label class="form__label" for="area-name">Name</label>
            <input class="form__input" id="area-name" type="text" name="name" value="<?= htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__field">
            <label class="form__label" for="area-key">Area Key</label>
            <input class="form__input" id="area-key" type="text" name="area_key" value="<?= htmlspecialchars((string)$areaKey, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__field">
            <label class="form__label" for="area-icon">Icon</label>
            <input class="form__input" id="area-icon" type="text" name="icon" value="<?= htmlspecialchars((string)$icon, ENT_QUOTES, 'UTF-8') ?>" placeholder="icon-home" />
        </div>

        <div class="form__field">
            <label class="form__label" for="area-start-path">Startpfad</label>
            <input class="form__input" id="area-start-path" type="text" name="start_path" value="<?= htmlspecialchars((string)$startPath, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__field">
            <label class="form__label" for="area-description">Beschreibung</label>
            <textarea class="form__textarea" id="area-description" name="description"><?= htmlspecialchars((string)$description, ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form__field">
            <label class="form__label" for="area-sort-order">Sortierung</label>
            <input class="form__input" id="area-sort-order" type="number" name="sort_order" value="<?= htmlspecialchars((string)$sortOrder, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__field">
            <label class="form__label">
                <input type="checkbox" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?> /> Aktiv
            </label>
        </div>

        <div class="form__field">
            <label class="form__label">
                <input type="checkbox" name="is_external" value="1" <?= $isExternal ? 'checked' : '' ?> /> Externes System
            </label>
        </div>

        <div class="form__actions form__actions--end">
            <button class="btn btn--primary btn--md" type="submit">Speichern</button>
            <a class="btn btn--ghost btn--md" href="/development/web-control/areas">Abbrechen</a>
        </div>
    </form>
</section>
