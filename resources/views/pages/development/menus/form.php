<?php declare(strict_types=1); ?>
<section class="menu-form">
    <h1><?= htmlspecialchars($pageTitle ?? 'Menü', ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php
    $menu = $menu ?? [];
    $id = $menu['id'] ?? '';
    $name = $menu['name'] ?? '';
    $slug = $menu['slug'] ?? '';
    $areaId = $menu['area_id'] ?? '';
    $areas = $areas ?? [];
    $areaLabel = $areaLabel ?? '';
    $isEdit = $id !== '';
    $action = $isEdit ? '/development/web-control/menus/edit' : '/development/web-control/menus/create';
    ?>

    <form method="post" action="<?= $action ?>" class="form">
        <?php if ($id !== ''): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?>" />
        <?php endif; ?>

        <?php if ($isEdit): ?>
            <div class="form__field">
                <label class="form__label">Area</label>
                <div><?= htmlspecialchars((string) ($areaLabel !== '' ? $areaLabel : 'Unbekannt'), ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        <?php else: ?>
            <div class="form__field">
                <label class="form__label" for="menu-area">Area</label>
                <select class="form__input" id="menu-area" name="area_id">
                    <option value="">— Bitte wählen —</option>
                    <?php foreach ($areas as $a): ?>
                        <option value="<?= htmlspecialchars((string) $a['id'], ENT_QUOTES, 'UTF-8') ?>" <?= ((string) $a['id'] === (string) $areaId) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $a['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (empty($areas)): ?>
                <p>Alle Areas haben bereits ein Menü.</p>
            <?php endif; ?>
        <?php endif; ?>

        <div class="form__field">
            <label class="form__label" for="menu-name">Name</label>
            <input class="form__input" id="menu-name" type="text" name="name" value="<?= htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__field">
            <label class="form__label" for="menu-slug">Slug</label>
            <input class="form__input" id="menu-slug" type="text" name="slug" value="<?= htmlspecialchars((string) $slug, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__actions form__actions--end">
            <button class="btn btn--primary btn--md" type="submit">Speichern</button>
            <a class="btn btn--ghost btn--md" href="/development/web-control/menus">Abbrechen</a>
        </div>
    </form>
</section>
