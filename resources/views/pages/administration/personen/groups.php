<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); ?>
<section class="content-card">
    <p class="eyebrow">Person</p>
    <h1><?= $e($person['display_name'] ?? ('Person #' . ($person['person_id'] ?? ''))) ?></h1>
    <p>E-Mail: <?= $e($person['email'] ?? '—') ?> · Subject: <code><?= $e($person['subject_uuid'] ?? 'wird bei Zuweisung erstellt') ?></code></p>
    <?php foreach (($errors ?? []) as $error): ?><p class="notice notice--danger"><?= $e($error) ?></p><?php endforeach; ?>
</section>
<section class="content-card">
    <h2>Aktuelle Gruppen</h2>
    <table class="data-table">
        <thead><tr><th>System</th><th>Gruppe</th><th>Ablauf</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach (($assignedGroups ?? []) as $group): ?>
            <tr>
                <td><code><?= $e($group['system_key']) ?></code></td>
                <td><code><?= $e($group['group_key']) ?></code> — <?= $e($group['group_name']) ?></td>
                <td><?= $e($group['expires_at'] ?? '') ?></td>
                <td><?= ((int) ($group['is_expired'] ?? 0) === 1) ? 'abgelaufen' : 'aktiv' ?></td>
                <td>
                    <form method="post" action="/administration/personen/<?= $e($person['person_id']) ?>/gruppen/<?= $e($group['group_id']) ?>/remove">
                        <button type="submit">Entfernen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (($assignedGroups ?? []) === []): ?><tr><td colspan="5">Keine Gruppen zugewiesen.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
<section class="content-card">
    <h2>Gruppe zuweisen</h2>
    <form method="post" action="/administration/personen/<?= $e($person['person_id'] ?? 0) ?>/gruppen" class="form-grid">
        <label>Gruppe
            <select name="group_id" required>
                <?php foreach (($availableGroups ?? []) as $group): ?>
                    <?php if ((int) ($group['is_active'] ?? 0) !== 1 || (int) ($group['is_assignable'] ?? 0) !== 1) { continue; } ?>
                    <option value="<?= $e($group['id']) ?>"><?= $e($group['system_key']) ?>.<?= $e($group['key_name']) ?> — <?= $e($group['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Ablaufdatum <input type="datetime-local" name="expires_at"></label>
        <label class="span-full">Notiz <textarea name="note"></textarea></label>
        <div class="span-full"><button class="button" type="submit">Zuweisen</button></div>
    </form>
</section>
