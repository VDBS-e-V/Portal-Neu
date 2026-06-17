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
$person = $person ?? [];
$contacts = $contacts ?? [];
$addresses = $addresses ?? [];
$groups = $groups ?? [];
$message = (string) ($message ?? '');

$personId = (int) ($person['id'] ?? 0);
$label = trim((string) ($person['display_name'] ?? ''));
if ($label === '') {
    $label = trim(trim((string) ($person['first_name'] ?? '')) . ' ' . trim((string) ($person['last_name'] ?? '')));
}
if ($label === '') {
    $label = 'Person #' . $personId;
}
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Personenverwaltung</p>
            <h1 class="page-title__title"><?= $e($label) ?></h1>
            <p class="page-title__lead">Detailansicht mit Login, Gruppen, Kontakten, Adressen und Audit-Verlauf.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--primary" href="/verwaltung/personen/<?= $personId ?>/edit">Bearbeiten</a>
                <a class="btn btn--outline" href="/verwaltung/personen/<?= $personId ?>/gruppen">Gruppen</a>
                <a class="btn btn--outline" href="/verwaltung/personen/<?= $personId ?>/audit">Audit</a>
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

    <div class="grid">
        <article class="summary-card summary-card--primary">
            <p class="summary-card__kicker">Person</p>
            <h2 class="summary-card__title"><?= $e($label) ?></h2>
            <p class="summary-card__text">
                <span class="<?= $statusBadge($person['status'] ?? '') ?>"><?= $e($person['status'] ?? '') ?></span>
            </p>
        </article>

        <article class="summary-card summary-card--info">
            <p class="summary-card__kicker">Login</p>
            <h2 class="summary-card__title"><?= $e($person['login_email'] ?? $person['email'] ?? 'Kein Login') ?></h2>
            <p class="summary-card__text">User-ID: <?= (int) ($person['user_id'] ?? $person['login_id'] ?? 0) ?></p>
        </article>
    </div>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <h2 class="table-block__title">Stammdaten</h2>
        </div>
        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--compact table--striped">
                <tbody>
                <?php foreach ([
                    'ID' => $personId,
                    'Anrede' => $person['salutation'] ?? '',
                    'Titel' => $person['title'] ?? '',
                    'Vorname' => $person['first_name'] ?? '',
                    'Weitere Vornamen' => $person['middle_name'] ?? '',
                    'Nachname' => $person['last_name'] ?? '',
                    'Rufname' => $person['preferred_name'] ?? '',
                    'Pronomen' => $person['pronouns'] ?? '',
                    'Erstellt' => $person['created_at'] ?? '',
                    'Geändert' => $person['updated_at'] ?? '',
                ] as $key => $value): ?>
                    <tr>
                        <th><?= $e($key) ?></th>
                        <td><?= $e($value) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section>
    <div class="grid">
        <div class="table-block table-block--card">
            <div class="table-block__header">
                <h2 class="table-block__title">Gruppen</h2>
                <div class="table-block__actions">
                    <a class="btn btn--sm btn--outline" href="/verwaltung/personen/<?= $personId ?>/gruppen">Bearbeiten</a>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table table--compact table--striped">
                    <tbody>
                    <?php if ($groups === []): ?>
                        <tr><td>Keine Gruppen zugewiesen.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($groups as $group): ?>
                        <tr>
                            <td><?= $e($group['name'] ?? $group['group_key'] ?? '') ?></td>
                            <td><code><?= $e($group['group_key'] ?? '') ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-block table-block--card">
            <div class="table-block__header">
                <h2 class="table-block__title">Kontakte</h2>
                <div class="table-block__actions">
                    <a class="btn btn--sm btn--outline" href="/verwaltung/personen/<?= $personId ?>/kontakte">Bearbeiten</a>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table table--compact table--striped">
                    <tbody>
                    <?php if ($contacts === []): ?>
                        <tr><td>Keine Kontakte vorhanden.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?= $e($contact['label'] ?? $contact['type'] ?? '') ?></td>
                            <td><?= $e($contact['value'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-block table-block--card">
            <div class="table-block__header">
                <h2 class="table-block__title">Adressen</h2>
                <div class="table-block__actions">
                    <a class="btn btn--sm btn--outline" href="/verwaltung/personen/<?= $personId ?>/adressen">Bearbeiten</a>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table table--compact table--striped">
                    <tbody>
                    <?php if ($addresses === []): ?>
                        <tr><td>Keine Adressen vorhanden.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($addresses as $address): ?>
                        <tr>
                            <td><?= $e($address['label'] ?? $address['type'] ?? '') ?></td>
                            <td>
                                <?= $e(trim((string) ($address['street'] ?? ''))) ?>
                                <?= $e(trim((string) ($address['postal_code'] ?? ''))) ?>
                                <?= $e(trim((string) ($address['city'] ?? ''))) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
