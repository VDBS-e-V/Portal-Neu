<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $selected = array_flip(array_map('intval', $selectedPermissionIds ?? [])); ?>
<section class="content-card">
    <h1>Permissions für <?= $e($group['name'] ?? '') ?></h1>
    <p><code><?= $e($group['system_key'] ?? '') ?>.<?= $e($group['key_name'] ?? '') ?></code></p>
    <?php foreach (($errors ?? []) as $error): ?><p class="notice notice--danger"><?= $e($error) ?></p><?php endforeach; ?>
    <form method="post" action="/administration/gruppen/<?= $e($group['id'] ?? 0) ?>/permissions">
        <table class="data-table">
            <thead><tr><th></th><th>Permission</th><th>Name</th><th>Kategorie</th></tr></thead>
            <tbody>
            <?php foreach (($permissions ?? []) as $permission): ?>
                <tr>
                    <td><input type="checkbox" name="permission_ids[]" value="<?= $e($permission['id']) ?>" <?= isset($selected[(int) $permission['id']]) ? 'checked' : '' ?>></td>
                    <td><code><?= $e($permission['key_name']) ?></code></td>
                    <td><?= $e($permission['name']) ?></td>
                    <td><?= $e($permission['category'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><button class="button" type="submit">Permissions speichern</button> <a href="/administration/gruppen/<?= $e($group['id'] ?? 0) ?>">Abbrechen</a></p>
    </form>
</section>
