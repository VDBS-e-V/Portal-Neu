<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $group = $group ?? []; ?>
<section class="content-card">
    <h1><?= $e($pageTitle ?? 'Gruppe') ?></h1>
    <?php foreach (($errors ?? []) as $error): ?><p class="notice notice--danger"><?= $e($error) ?></p><?php endforeach; ?>
    <form method="post" action="<?= $e($action ?? '') ?>" class="form-grid">
        <label>System
            <select name="system_id" required>
                <?php foreach (($systems ?? []) as $system): ?>
                    <option value="<?= $e($system['id']) ?>" <?= (int) ($group['system_id'] ?? 0) === (int) $system['id'] ? 'selected' : '' ?>><?= $e($system['name']) ?> (<?= $e($system['key_name']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Key <input name="key_name" value="<?= $e($group['key_name'] ?? '') ?>" required pattern="[a-z0-9][a-z0-9-]*"></label>
        <label>Name <input name="name" value="<?= $e($group['name'] ?? '') ?>" required></label>
        <label>Sortierung <input type="number" name="sorting" value="<?= $e($group['sorting'] ?? 100) ?>"></label>
        <label><input type="checkbox" name="is_active" value="1" <?= ((int) ($group['is_active'] ?? 1) === 1) ? 'checked' : '' ?>> aktiv</label>
        <label><input type="checkbox" name="is_system" value="1" <?= ((int) ($group['is_system'] ?? 0) === 1) ? 'checked' : '' ?>> Systemgruppe</label>
        <label><input type="checkbox" name="is_default" value="1" <?= ((int) ($group['is_default'] ?? 0) === 1) ? 'checked' : '' ?>> Default-Gruppe</label>
        <label><input type="checkbox" name="is_assignable" value="1" <?= ((int) ($group['is_assignable'] ?? 1) === 1) ? 'checked' : '' ?>> manuell zuweisbar</label>
        <label class="span-full">Beschreibung <textarea name="description"><?= $e($group['description'] ?? '') ?></textarea></label>
        <div class="span-full"><button class="button" type="submit">Speichern</button> <a href="/administration/gruppen">Abbrechen</a></div>
    </form>
</section>
