<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$group = $group ?? [];
$members = $members ?? [];
$groupId = (int) ($group['id'] ?? 0);
$message = (string) ($message ?? '');
$isSystem = !empty($group['is_system']);
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $e($group['name'] ?? 'Gruppe') ?></h1>
            <p><code><?= $e($group['group_key'] ?? '') ?></code></p>
        </div>

        <p>
            <a class="button" href="/verwaltung/gruppen">Zurück</a>
            <a class="button button-primary" href="/verwaltung/gruppen/<?= $groupId ?>/edit">Bearbeiten</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <nav class="tabs">
        <a href="/verwaltung/gruppen/<?= $groupId ?>">Übersicht</a>
        <a href="/verwaltung/gruppen/<?= $groupId ?>/mitglieder">Mitglieder</a>
    </nav>

    <section class="card">
        <h2>Details</h2>

        <dl>
            <dt>ID</dt>
            <dd><?= $groupId ?></dd>

            <dt>Key</dt>
            <dd><code><?= $e($group['group_key'] ?? '') ?></code></dd>

            <dt>Name</dt>
            <dd><?= $e($group['name'] ?? '') ?></dd>

            <dt>Beschreibung</dt>
            <dd><?= $e($group['description'] ?? '') ?></dd>

            <dt>Systemgruppe</dt>
            <dd><?= $isSystem ? 'ja' : 'nein' ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Mitglieder</h2>

        <?php if ($members === []): ?>
            <p>Keine Mitglieder zugewiesen.</p>
        <?php else: ?>
            <ul>
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

                    <li>
                        <a href="/verwaltung/personen/<?= $memberId ?>">
                            <?= $e($label) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <?php if (!$isSystem): ?>
        <section class="card danger-zone">
            <h2>Gruppe löschen</h2>

            <form method="post" action="/verwaltung/gruppen/<?= $groupId ?>/delete">
                <button type="submit">Gruppe löschen</button>
            </form>
        </section>
    <?php endif; ?>
</section>