<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$pageGroups = $pageGroups ?? [];
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>PageGroups</h1>
            <p>Technische Seitengruppen für feingranulare Zugriffe.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/berechtigungen">Zurück zur Matrix</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Area</th>
            <th>PageGroup</th>
            <th>Name</th>
            <th>Beschreibung</th>
            <th>Startpfad</th>
            <th>Sortierung</th>
            <th>Aktiv</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($pageGroups === []): ?>
            <tr>
                <td colspan="8">Keine PageGroups vorhanden.</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($pageGroups as $pageGroup): ?>
            <tr>
                <td><?= (int) ($pageGroup['id'] ?? 0) ?></td>
                <td><code><?= $e($pageGroup['area_key'] ?? '') ?></code></td>
                <td><code><?= $e($pageGroup['page_group_key'] ?? '') ?></code></td>
                <td><?= $e($pageGroup['name'] ?? '') ?></td>
                <td><?= $e($pageGroup['description'] ?? '') ?></td>
                <td><?= $e($pageGroup['start_path'] ?? '') ?></td>
                <td><?= $e($pageGroup['sort_order'] ?? '') ?></td>
                <td><?= !empty($pageGroup['is_active']) ? 'ja' : 'nein' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
