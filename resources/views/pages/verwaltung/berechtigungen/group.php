<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$group = $group ?? [];
$pageGroups = $pageGroups ?? [];
$assignedIds = $assignedIds ?? [];
$groupId = (int) ($group['id'] ?? 0);
$message = (string) ($message ?? '');

$pageGroupsByArea = [];

foreach ($pageGroups as $pageGroup) {
    $areaKey = (string) ($pageGroup['area_key'] ?? 'unknown');
    $pageGroupsByArea[$areaKey][] = $pageGroup;
}
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Berechtigungen bearbeiten') ?></h1>
            <p><code><?= $e($group['group_key'] ?? '') ?></code></p>
        </div>

        <p>
            <a class="button" href="/verwaltung/berechtigungen">Zurück</a>
            <a class="button" href="/verwaltung/gruppen/<?= $groupId ?>">Gruppe anzeigen</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <form method="post" action="/verwaltung/berechtigungen/gruppen/<?= $groupId ?>">
        <?php if ($pageGroupsByArea === []): ?>
            <section class="card">
                <p>Keine PageGroups vorhanden.</p>
            </section>
        <?php endif; ?>

        <?php foreach ($pageGroupsByArea as $areaKey => $areaPageGroups): ?>
            <section class="card">
                <h2><?= $e($areaKey) ?></h2>

                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Zugriff</th>
                        <th>PageGroup</th>
                        <th>Name</th>
                        <th>Pfad</th>
                        <th>Aktiv</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($areaPageGroups as $pageGroup): ?>
                        <?php $pageGroupId = (int) ($pageGroup['id'] ?? 0); ?>
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    name="page_group_ids[]"
                                    value="<?= $pageGroupId ?>"
                                    <?= !empty($assignedIds[$pageGroupId]) ? 'checked' : '' ?>
                                >
                            </td>
                            <td><code><?= $e($pageGroup['area_key'] ?? '') ?>.<?= $e($pageGroup['page_group_key'] ?? '') ?></code></td>
                            <td><?= $e($pageGroup['name'] ?? '') ?></td>
                            <td><?= $e($pageGroup['start_path'] ?? '') ?></td>
                            <td><?= !empty($pageGroup['is_active']) ? 'ja' : 'nein' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endforeach; ?>

        <p>
            <button type="submit">Berechtigungen speichern</button>
        </p>
    </form>
</section>
