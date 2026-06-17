<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$stats = $stats ?? [];
$quickLinks = $quickLinks ?? [];
$latestPersons = $latestPersons ?? [];
$latestAuditEntries = $latestAuditEntries ?? [];
$openTasks = $openTasks ?? [];

$number = static fn (mixed $value): string => number_format((int) $value, 0, ',', '.');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Verwaltung</h1>
            <p>Übersicht über Personen, Logins, Gruppen, Berechtigungen und offene Aufgaben.</p>
        </div>
    </header>

    <section class="dashboard-grid">
        <article class="card metric-card">
            <h2>Personen</h2>
            <p class="metric"><?= $number($stats['persons_total'] ?? 0) ?></p>
            <p>
                Aktiv: <?= $number($stats['persons_active'] ?? 0) ?> ·
                Deaktiviert: <?= $number($stats['persons_disabled'] ?? 0) ?>
            </p>
            <?php if ((int) ($stats['persons_erasure_requested'] ?? 0) > 0): ?>
                <p>DSGVO beantragt: <?= $number($stats['persons_erasure_requested']) ?></p>
            <?php endif; ?>
        </article>

        <article class="card metric-card">
            <h2>Logins</h2>
            <p class="metric"><?= $number($stats['logins_total'] ?? 0) ?></p>
            <p>
                Aktiv: <?= $number($stats['logins_active'] ?? 0) ?> ·
                Eingeladen: <?= $number($stats['logins_invited'] ?? 0) ?>
            </p>
        </article>

        <article class="card metric-card">
            <h2>Gruppen</h2>
            <p class="metric"><?= $number($stats['groups_total'] ?? 0) ?></p>
            <p>Systemgruppen: <?= $number($stats['groups_system'] ?? 0) ?></p>
        </article>

        <article class="card metric-card">
            <h2>PageGroups</h2>
            <p class="metric"><?= $number($stats['page_groups_total'] ?? 0) ?></p>
            <p>Aktiv: <?= $number($stats['page_groups_active'] ?? 0) ?></p>
        </article>

        <article class="card metric-card">
            <h2>Einladungen</h2>
            <p class="metric"><?= $number($stats['pending_invitations'] ?? 0) ?></p>
            <p>offen</p>
        </article>

        <article class="card metric-card">
            <h2>DSGVO</h2>
            <p class="metric"><?= $number($stats['open_erasure_requests'] ?? 0) ?></p>
            <p>
                Beantragt: <?= $number($stats['requested_erasure_requests'] ?? 0) ?> ·
                Freigegeben: <?= $number($stats['approved_erasure_requests'] ?? 0) ?>
            </p>
        </article>
    </section>

    <section class="content-grid">
        <article class="card">
            <h2>Schnellzugriffe</h2>

            <div class="quick-link-grid">
                <?php foreach ($quickLinks as $link): ?>
                    <a class="quick-link-card" href="<?= $e($link['href'] ?? '#') ?>">
                        <strong><?= $e($link['label'] ?? '') ?></strong>
                        <span><?= $e($link['description'] ?? '') ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </article>

        <article class="card">
            <h2>Offene Aufgaben</h2>

            <?php if ($openTasks === []): ?>
                <p>Keine offenen Aufgaben.</p>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach ($openTasks as $task): ?>
                        <li>
                            <a href="<?= $e($task['href'] ?? '#') ?>">
                                <strong><?= $e($task['label'] ?? '') ?></strong>
                                <span><?= $e($task['title'] ?? '') ?></span>
                                <small><?= $e($task['subtitle'] ?? '') ?></small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </section>

    <section class="content-grid">
        <article class="card">
            <h2>Neue Personen</h2>

            <table class="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Login</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($latestPersons === []): ?>
                    <tr>
                        <td colspan="4">Keine Personen vorhanden.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($latestPersons as $person): ?>
                    <?php
                    $personId = (int) ($person['id'] ?? 0);
                    $label = trim((string) ($person['display_name'] ?? ''));

                    if ($label === '') {
                        $label = trim((string) ($person['login_email'] ?? 'Person #' . $personId));
                    }
                    ?>
                    <tr>
                        <td><?= $personId ?></td>
                        <td><a href="/verwaltung/personen/<?= $personId ?>"><?= $e($label) ?></a></td>
                        <td><?= $e($person['login_email'] ?? '') ?></td>
                        <td><?= $e($person['status'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </article>

        <article class="card">
            <h2>Letzte Audit-Einträge</h2>

            <table class="data-table">
                <thead>
                <tr>
                    <th>Zeitpunkt</th>
                    <th>Aktion</th>
                    <th>Entität</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($latestAuditEntries === []): ?>
                    <tr>
                        <td colspan="3">Keine Audit-Einträge vorhanden.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($latestAuditEntries as $entry): ?>
                    <?php $entryId = (int) ($entry['id'] ?? 0); ?>
                    <tr>
                        <td><?= $e($entry['occurred_at'] ?? '') ?></td>
                        <td>
                            <a href="/verwaltung/audit/<?= $entryId ?>">
                                <code><?= $e($entry['action'] ?? '') ?></code>
                            </a>
                        </td>
                        <td>
                            <?= $e($entry['entity_type'] ?? '') ?>
                            <?php if (!empty($entry['entity_id'])): ?>
                                #<?= (int) $entry['entity_id'] ?>
                            <?php endif; ?>
                            <?php if (!empty($entry['entity_label'])): ?>
                                <br><?= $e($entry['entity_label']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </article>
    </section>
</section>
