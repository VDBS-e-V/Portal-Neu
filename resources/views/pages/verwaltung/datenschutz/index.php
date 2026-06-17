<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$requests = $requests ?? [];
$filters = $filters ?? [];
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>DSGVO-Löschung</h1>
            <p>Löschersuchen prüfen, freigeben und personenbezogene Daten anonymisieren.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen">Personen verwalten</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <form method="get" action="/verwaltung/datenschutz" class="filter-form">
        <label>
            Suche
            <input type="search" name="q" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Person, E-Mail, Grund">
        </label>

        <label>
            Status
            <?php $selectedStatus = (string) ($filters['status'] ?? ''); ?>
            <select name="status">
                <?php foreach ([
                    '' => 'Alle',
                    'requested' => 'Beantragt',
                    'approved' => 'Freigegeben',
                    'rejected' => 'Abgelehnt',
                    'completed' => 'Abgeschlossen',
                    'cancelled' => 'Storniert',
                ] as $value => $label): ?>
                    <option value="<?= $e($value) ?>" <?= $selectedStatus === $value ? 'selected' : '' ?>>
                        <?= $e($label) ?>
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
            <th>Person</th>
            <th>Status</th>
            <th>Grund</th>
            <th>Beantragt</th>
            <th>Abgeschlossen</th>
            <th>Aktion</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($requests === []): ?>
            <tr>
                <td colspan="7">Keine DSGVO-Vorgänge gefunden.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($requests as $erasure): ?>
            <?php $requestId = (int) ($erasure['id'] ?? 0); ?>
            <tr>
                <td><?= $requestId ?></td>
                <td>
                    <?= $e($erasure['display_name'] ?? '') ?>
                    <?php if (!empty($erasure['login_email'])): ?>
                        <br><?= $e($erasure['login_email']) ?>
                    <?php endif; ?>
                    <br>
                    <small>Person #<?= (int) ($erasure['person_id'] ?? 0) ?></small>
                </td>
                <td><?= $e($erasure['status'] ?? '') ?></td>
                <td><?= $e($erasure['reason'] ?? '') ?></td>
                <td><?= $e($erasure['requested_at'] ?? '') ?></td>
                <td><?= $e($erasure['completed_at'] ?? '') ?></td>
                <td>
                    <a href="/verwaltung/datenschutz/<?= $requestId ?>">Details</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
