<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $system = $system ?? []; ?>
<section class="content-card">
    <h1><?= $e($pageTitle ?? 'System') ?></h1>
    <?php foreach (($errors ?? []) as $error): ?><p class="notice notice--danger"><?= $e($error) ?></p><?php endforeach; ?>
    <form method="post" action="<?= $e($action ?? '') ?>" class="form-grid">
        <label>Key <input name="key_name" value="<?= $e($system['key_name'] ?? '') ?>" required pattern="[a-z0-9][a-z0-9-]*"></label>
        <label>Name <input name="name" value="<?= $e($system['name'] ?? '') ?>" required></label>
        <label>Sortierung <input type="number" name="sorting" value="<?= $e($system['sorting'] ?? 100) ?>"></label>
        <label><input type="checkbox" name="is_active" value="1" <?= ((int) ($system['is_active'] ?? 1) === 1) ? 'checked' : '' ?>> aktiv</label>
        <label><input type="checkbox" name="is_external" value="1" <?= ((int) ($system['is_external'] ?? 0) === 1) ? 'checked' : '' ?>> extern</label>
        <label class="span-full">Beschreibung <textarea name="description"><?= $e($system['description'] ?? '') ?></textarea></label>
        <div class="span-full"><button class="button" type="submit">Speichern</button> <a href="/administration/systeme">Abbrechen</a></div>
    </form>
</section>
