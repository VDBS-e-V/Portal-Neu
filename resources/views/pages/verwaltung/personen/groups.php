<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$person = $person ?? [];
$assignedGroups = $assignedGroups ?? [];
$allGroups = $allGroups ?? [];
$personId = (int) ($person['id'] ?? 0);
$message = (string) ($message ?? '');

$assignedIds = [];

foreach ($assignedGroups as $group) {
    $assignedIds[(int) ($group['id'] ?? 0)] = true;
}
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Gruppen') ?></h1>
            <p>Gruppen dieser Person zuweisen oder entfernen.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen/<?= $personId ?>">Zurück zur Person</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success">
            <?= $e($message) ?>
        </div>
    <?php endif; ?>

    <nav class="tabs">
        <a href="/verwaltung/personen/<?= $personId ?>">Übersicht</a>
        <a href="/verwaltung/personen/<?= $personId ?>/kontakte">Kontakte</a>
        <a href="/verwaltung/personen/<?= $personId ?>/adressen">Adressen</a>
        <a href="/verwaltung/personen/<?= $personId ?>/gruppen">Gruppen</a>
    </nav>

    <form method="post" action="/verwaltung/personen/<?= $personId ?>/gruppen">
        <table class="data-table">
            <thead>
            <tr>
                <th>Zugewiesen</th>
                <th>Gruppe</th>
                <th>Name</th>
                <th>Systemgruppe</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($allGroups as $group): ?>
                <?php $groupId = (int) ($group['id'] ?? 0); ?>
                <tr>
                    <td>
                        <input
                            type="checkbox"
                            name="group_ids[]"
                            value="<?= $groupId ?>"
                            <?= isset($assignedIds[$groupId]) ? 'checked' : '' ?>
                        >
                    </td>
                    <td><code><?= $e($group['group_key'] ?? '') ?></code></td>
                    <td><?= $e($group['name'] ?? '') ?></td>
                    <td><?= !empty($group['is_system']) ? 'ja' : 'nein' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <p>
            <button type="submit">Gruppen speichern</button>
        </p>
    </form>
</section>