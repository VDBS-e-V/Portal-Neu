<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$persons = $persons ?? [];
$groups = $groups ?? [];
$filters = $filters ?? [];
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Personen</h1>
            <p>Personen, optionale Login-Konten und Gruppenzuweisungen verwalten.</p>
        </div>

        <p>
            <a class="button button-primary" href="/verwaltung/personen/create">Person anlegen</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success">
            <?= $e($message) ?>
        </div>
    <?php endif; ?>

    <form method="get" action="/verwaltung/personen" class="filter-form">
        <label>
            Suche
            <input type="search" name="q" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Name oder E-Mail">
        </label>

        <label>
            Status
            <select name="status">
                <?php
                $selectedStatus = (string) ($filters['status'] ?? '');
                $statusOptions = [
                    '' => 'Alle',
                    'active' => 'Aktiv',
                    'disabled' => 'Deaktiviert',
                    'erasure_requested' => 'Löschung beantragt',
                    'erased' => 'Gelöscht/anonymisiert',
                ];
                ?>
                <?php foreach ($statusOptions as $value => $label): ?>
                    <option value="<?= $e($value) ?>" <?= $selectedStatus === $value ? 'selected' : '' ?>>
                        <?= $e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Gruppe
            <select name="group_id">
                <?php $selectedGroupId = (int) ($filters['group_id'] ?? 0); ?>
                <option value="0">Alle Gruppen</option>
                <?php foreach ($groups as $group): ?>
                    <?php $groupId = (int) ($group['id'] ?? 0); ?>
                    <option value="<?= $groupId ?>" <?= $selectedGroupId === $groupId ? 'selected' : '' ?>>
                        <?= $e($group['group_key'] ?? '') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit">Filtern</button>
    </form>

    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Login</th>
            <th>Status</th>
            <th>Gruppen</th>
            <th>Aktionen</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($persons === []): ?>
            <tr>
                <td colspan="6">Keine Personen gefunden.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($persons as $person): ?>
            <?php
            $personId = (int) ($person['id'] ?? 0);
            $displayName = trim((string) ($person['display_name'] ?? ''));
            $preferredName = trim((string) ($person['preferred_name'] ?? ''));
            $fullName = trim(trim((string) ($person['first_name'] ?? '')) . ' ' . trim((string) ($person['last_name'] ?? '')));
            $label = $displayName !== '' ? $displayName : ($preferredName !== '' ? $preferredName : ($fullName !== '' ? $fullName : 'Person #' . $personId));
            ?>
            <tr>
                <td><?= $personId ?></td>
                <td>
                    <a href="/verwaltung/personen/<?= $personId ?>">
                        <?= $e($label) ?>
                    </a>
                </td>
                <td><?= $e($person['login_email'] ?? '') ?></td>
                <td><?= $e($person['status'] ?? $person['login_status'] ?? '') ?></td>
                <td><?= $e($person['group_keys'] ?? '') ?></td>
                <td>
                    <a href="/verwaltung/personen/<?= $personId ?>">Anzeigen</a>
                    |
                    <a href="/verwaltung/personen/<?= $personId ?>/edit">Bearbeiten</a>
                    |
                    <a href="/verwaltung/personen/<?= $personId ?>/gruppen">Gruppen</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>