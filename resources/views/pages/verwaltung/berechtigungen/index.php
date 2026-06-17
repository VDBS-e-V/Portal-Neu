<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$statusBadge = static function (mixed $status): string {
    $status = strtolower(trim((string) $status));

    return match ($status) {
        'active', 'accepted', 'completed', 'used', 'success' => 'table-badge table-badge--success',
        'disabled', 'revoked', 'cancelled', 'rejected', 'expired', 'error' => 'table-badge table-badge--danger',
        'invited', 'pending', 'requested', 'approved', 'warning' => 'table-badge table-badge--warning',
        default => 'table-badge table-badge--neutral',
    };
};

$messageText = static function (string $message): string {
    return match ($message) {
        'created' => 'Der Datensatz wurde angelegt.',
        'updated' => 'Die Änderungen wurden gespeichert.',
        'deleted' => 'Der Datensatz wurde gelöscht.',
        'status' => 'Der Status wurde geändert.',
        'revoked' => 'Die Einladung wurde widerrufen.',
        'requested' => 'Der Vorgang wurde beantragt.',
        'approved' => 'Der Vorgang wurde freigegeben.',
        'rejected' => 'Der Vorgang wurde abgelehnt.',
        'cancelled' => 'Der Vorgang wurde storniert.',
        'completed' => 'Der Vorgang wurde abgeschlossen.',
        default => $message,
    };
};
?>
<?php
$groups = $groups ?? [];
$pageGroups = $pageGroups ?? [];
$accessMatrix = $accessMatrix ?? [];
$message = (string) ($message ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">Berechtigungen</h1>
            <p class="page-title__lead">PageGroup-Zugriffe je Berechtigungsgruppe prüfen und bearbeiten.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--outline" href="/verwaltung/berechtigungen/page-groups">PageGroups</a>
            </div>
        </div>
    </div>
</section>

<section class="no-padding">
    <?php
    $navFile = __DIR__ . '/../../../partials/verwaltung_nav.php';
    if (is_file($navFile)) {
        require $navFile;
    }
    ?>
</section>

<section>
    
<?php if (trim((string) ($message ?? '')) !== ''): ?>
    <div class="form__notice form__notice--success">
        <?= $e($messageText((string) $message)) ?>
    </div>
<?php endif; ?>

<?php if (($errors ?? []) !== []): ?>
    <div class="form__notice form__notice--error">
        <strong>Bitte prüfen:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Berechtigungsmatrix</h2>
            <p class="table-block__subtitle">Ein Haken bedeutet Zugriff auf die jeweilige Area.PageGroup.</p>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--compact table--bordered table--hover">
                <thead>
                <tr>
                    <th>Gruppe</th>
                    <?php foreach ($pageGroups as $pageGroup): ?>
                        <th><?= $e(($pageGroup['area_key'] ?? '') . '.' . ($pageGroup['page_group_key'] ?? '')) ?></th>
                    <?php endforeach; ?>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $group): ?>
                    <?php $groupId = (int) ($group['id'] ?? 0); ?>
                    <tr>
                        <th>
                            <?= $e($group['name'] ?? $group['group_key'] ?? '') ?>
                            <span class="table__subtext"><code><?= $e($group['group_key'] ?? '') ?></code></span>
                        </th>
                        <?php foreach ($pageGroups as $pageGroup): ?>
                            <?php $pageGroupId = (int) ($pageGroup['id'] ?? 0); ?>
                            <td class="table__cell--center">
                                <?= !empty($accessMatrix[$groupId][$pageGroupId]) ? '✓' : '—' ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="table__cell--actions">
                            <a class="btn btn--xs btn--outline" href="/verwaltung/berechtigungen/gruppen/<?= $groupId ?>">Bearbeiten</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($groups === []): ?>
                    <tr><td colspan="<?= count($pageGroups) + 2 ?>">Keine Gruppen vorhanden.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
