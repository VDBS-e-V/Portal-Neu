<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$groups = $groups ?? [];
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Gruppen</h1>
            <p>Berechtigungsgruppen verwalten. Systemgruppen sind geschützt.</p>
        </div>

        <p>
            <a class="button button-primary" href="/verwaltung/gruppen/create">Gruppe anlegen</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Key</th>
            <th>Name</th>
            <th>Beschreibung</th>
            <th>System</th>
            <th>Aktionen</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($groups === []): ?>
            <tr>
                <td colspan="6">Keine Gruppen gefunden.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($groups as $group): ?>
            <?php $groupId = (int) ($group['id'] ?? 0); ?>
            <tr>
                <td><?= $groupId ?></td>
                <td><code><?= $e($group['group_key'] ?? '') ?></code></td>
                <td>
                    <a href="/verwaltung/gruppen/<?= $groupId ?>">
                        <?= $e($group['name'] ?? '') ?>
                    </a>
                </td>
                <td><?= $e($group['description'] ?? '') ?></td>
                <td><?= !empty($group['is_system']) ? 'ja' : 'nein' ?></td>
                <td>
                    <a href="/verwaltung/gruppen/<?= $groupId ?>">Anzeigen</a>
                    |
                    <a href="/verwaltung/gruppen/<?= $groupId ?>/edit">Bearbeiten</a>
                    |
                    <a href="/verwaltung/gruppen/<?= $groupId ?>/mitglieder">Mitglieder</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>