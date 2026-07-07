<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $permission = $permission ?? []; ?>
<section class="content-card">
    <h1><?= $e($pageTitle ?? 'Permission') ?></h1>
    <?php foreach (($errors ?? []) as $error): ?><p class="notice notice--danger"><?= $e($error) ?></p><?php endforeach; ?>
    <form method="post" action="<?= $e($action ?? '') ?>" class="form-grid">
        <label>System
            <select name="system_id" required>
                <?php foreach (($systems ?? []) as $system): ?>
                    <option value="<?= $e($system['id']) ?>" <?= (int) ($permission['system_id'] ?? 0) === (int) $system['id'] ? 'selected' : '' ?>><?= $e($system['name']) ?> (<?= $e($system['key_name']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Key <input name="key_name" value="<?= $e($permission['key_name'] ?? '') ?>" required></label>
        <label>Name <input name="name" value="<?= $e($permission['name'] ?? '') ?>" required></label>
        <label>Kategorie <input name="category" value="<?= $e($permission['category'] ?? '') ?>"></label>
        <label><input type="checkbox" name="is_active" value="1" <?= ((int) ($permission['is_active'] ?? 1) === 1) ? 'checked' : '' ?>> aktiv</label>
        <label><input type="checkbox" name="is_system" value="1" <?= ((int) ($permission['is_system'] ?? 0) === 1) ? 'checked' : '' ?>> Systempermission</label>
        <label>Deprecated seit <input type="datetime-local" name="deprecated_at" value="<?= $e(isset($permission['deprecated_at']) ? str_replace(' ', 'T', substr((string) $permission['deprecated_at'], 0, 16)) : '') ?>"></label>
        <label>Deprecated-Grund <input name="deprecated_reason" value="<?= $e($permission['deprecated_reason'] ?? '') ?>"></label>
        <label class="span-full">Beschreibung <textarea name="description"><?= $e($permission['description'] ?? '') ?></textarea></label>
        <div class="span-full"><button class="button" type="submit">Speichern</button> <a href="/administration/permissions">Abbrechen</a></div>
    </form>
</section>
