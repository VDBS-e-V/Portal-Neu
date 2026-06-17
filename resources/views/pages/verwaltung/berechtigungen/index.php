<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$groups = $groups ?? [];
$pageGroups = $pageGroups ?? [];
$accessMatrix = $accessMatrix ?? [];
$message = (string) ($message ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Berechtigungen</h1>
            <p>PageGroup-Zugriffe pro Berechtigungsgruppe verwalten.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/berechtigungen/page-groups">PageGroups anzeigen</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <section class="card">
        <h2>Matrix</h2>

        <table class="data-table">
            <thead>
            <tr>
                <th>Gruppe</th>
                <?php foreach ($pageGroups as $pageGroup): ?>
                    <th>
                        <?= $e($pageGroup['area_key'] ?? '') ?>.<br>
                        <?= $e($pageGroup['page_group_key'] ?? '') ?>
                    </th>
                <?php endforeach; ?>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($groups === []): ?>
                <tr>
                    <td colspan="<?= count($pageGroups) + 2 ?>">Keine Gruppen gefunden.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($groups as $group): ?>
                <?php $groupId = (int) ($group['id'] ?? 0); ?>
                <tr>
                    <td>
                        <strong><?= $e($group['name'] ?? '') ?></strong><br>
                        <code><?= $e($group['group_key'] ?? '') ?></code>
                    </td>

                    <?php foreach ($pageGroups as $pageGroup): ?>
                        <?php $pageGroupId = (int) ($pageGroup['id'] ?? 0); ?>
                        <td>
                            <?= !empty($accessMatrix[$groupId][$pageGroupId]) ? 'ja' : 'nein' ?>
                        </td>
                    <?php endforeach; ?>

                    <td>
                        <a href="/verwaltung/berechtigungen/gruppen/<?= $groupId ?>">Bearbeiten</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
