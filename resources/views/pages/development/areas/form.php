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
    $name = $area['name'] ?? '';
    $slug = $area['slug'] ?? '';
    $description = $area['description'] ?? '';
    $isPublic = !empty($area['is_public']) ? 1 : 0;
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
            <label class="form__label" for="area-slug">Slug</label>
            <input class="form__input" id="area-slug" type="text" name="slug" value="<?= htmlspecialchars((string)$slug, ENT_QUOTES, 'UTF-8') ?>" />
        </div>

        <div class="form__field">
            <label class="form__label" for="area-description">Beschreibung</label>
            <textarea class="form__textarea" id="area-description" name="description"><?= htmlspecialchars((string)$description, ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form__field">
            <label class="form__label">
                <input type="checkbox" name="is_public" value="1" <?= $isPublic ? 'checked' : '' ?> /> Öffentlich
            </label>
        </div>

        <div class="form__actions form__actions--end">
            <button class="btn btn--primary btn--md" type="submit">Speichern</button>
            <a class="btn btn--ghost btn--md" href="/development/web-control/areas">Abbrechen</a>
        </div>
    </form>
</section>
