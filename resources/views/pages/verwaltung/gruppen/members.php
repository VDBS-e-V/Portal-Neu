<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$group = $group ?? [];
$members = $members ?? [];
$groupId = (int) ($group['id'] ?? 0);
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($pageTitle ?? 'Gruppenmitglieder') ?></h1>
            <p><code><?= $e($group['group_key'] ?? '') ?></code></p>
        </div>

        <p>
            <a class="button" href="/verwaltung/gruppen/<?= $groupId ?>">Zurück zur Gruppe</a>
            <a class="button button-primary" href="/verwaltung/personen">Personen verwalten</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <nav class="tabs">
        <a href="/verwaltung/gruppen/<?= $groupId ?>">Übersicht</a>
        <a href="/verwaltung/gruppen/<?= $groupId ?>/mitglieder">Mitglieder</a>
    </nav>

    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Zugewiesen am</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($members === []): ?>
            <tr>
                <td colspan="5">Keine Mitglieder zugewiesen.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($members as $member): ?>
            <?php
            $memberId = (int) ($member['id'] ?? 0);
            $label = trim((string) ($member['display_name'] ?? ''));

            if ($label === '') {
                $label = trim((string) ($member['email'] ?? $member['login_email'] ?? ''));
            }

            if ($label === '') {
                $label = 'Person #' . $memberId;
            }
            ?>

            <tr>
                <td><?= $memberId ?></td>
                <td>
                    <a href="/verwaltung/personen/<?= $memberId ?>">
                        <?= $e($label) ?>
                    </a>
                </td>
                <td><?= $e($member['status'] ?? '') ?></td>
                <td><?= $e($member['assigned_at'] ?? '') ?></td>
                <td>
                    <a href="/verwaltung/personen/<?= $memberId ?>/gruppen">Gruppen bearbeiten</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>