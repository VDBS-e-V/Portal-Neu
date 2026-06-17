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
$persons = $persons ?? [];
$groups = $groups ?? [];
$filters = $filters ?? [];
$message = (string) ($message ?? '');
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Verwaltung</p>
            <h1 class="page-title__title">Personen</h1>
            <p class="page-title__lead">Stammdaten, optionale Login-Zugänge, Kontakte, Adressen und Gruppenzuweisungen verwalten.</p>
        </div>
        <div class="page-title__side">
            <div class="page-title__actions btn-group">
                <a class="btn btn--primary" href="/verwaltung/personen/create">Person anlegen</a>
                <a class="btn btn--outline" href="/verwaltung">Zur Übersicht</a>
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

    <form class="form form--surface" method="get" action="/verwaltung/personen">
        <div class="form__grid form__grid--4 form__grid--compact">
            <div class="form__field form__field--span-2">
                <label class="form__label" for="personen-q">Suche</label>
                <input class="form__control" id="personen-q" name="q" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Name oder E-Mail">
            </div>

            <div class="form__field">
                <label class="form__label" for="personen-status">Status</label>
                <select class="form__control" id="personen-status" name="status">
                    <option value="">Alle</option>
                    <?php foreach (['active' => 'Aktiv', 'disabled' => 'Deaktiviert', 'invited' => 'Eingeladen', 'erasure_requested' => 'DSGVO beantragt'] as $value => $label): ?>
                        <option value="<?= $e($value) ?>" <?= (string) ($filters['status'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= $e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__field">
                <label class="form__label" for="personen-group">Gruppe</label>
                <select class="form__control" id="personen-group" name="group_id">
                    <option value="0">Alle Gruppen</option>
                    <?php foreach ($groups as $group): ?>
                        <?php $groupId = (int) ($group['id'] ?? 0); ?>
                        <option value="<?= $groupId ?>" <?= (int) ($filters['group_id'] ?? 0) === $groupId ? 'selected' : '' ?>>
                            <?= $e($group['name'] ?? $group['group_key'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__actions">
                <button class="btn btn--primary" type="submit">Filtern</button>
                <a class="btn btn--outline" href="/verwaltung/personen">Zurücksetzen</a>
            </div>
        </div>
    </form>
</section>

<section>
    <div class="table-block table-block--card">
        <div class="table-block__header">
            <p class="table-block__kicker">Verwaltung</p>
            <h2 class="table-block__title">Personenübersicht</h2>
            <p class="table-block__subtitle">Alle gefundenen Personen mit Login-Status und Aktionen.</p>
        </div>

        <div class="table-wrapper table-wrapper--bordered">
            <table class="table table--striped table--hover table--compact table--stack">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Status</th>
                    <th class="table__cell--actions">Aktionen</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($persons === []): ?>
                    <tr>
                        <td colspan="5">
                            <div class="table-empty">
                                <p class="table-empty__title">Keine Personen gefunden</p>
                                <p class="table-empty__text">Passe die Filter an oder lege eine neue Person an.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($persons as $person): ?>
                    <?php
                    $personId = (int) ($person['id'] ?? 0);
                    $label = trim((string) ($person['display_name'] ?? ''));
                    if ($label === '') {
                        $label = trim(trim((string) ($person['first_name'] ?? '')) . ' ' . trim((string) ($person['last_name'] ?? '')));
                    }
                    if ($label === '') {
                        $label = 'Person #' . $personId;
                    }
                    ?>
                    <tr>
                        <td data-label="ID" class="table__cell--nowrap">#<?= $personId ?></td>
                        <td data-label="Name" class="table__cell--strong">
                            <a href="/verwaltung/personen/<?= $personId ?>"><?= $e($label) ?></a>
                            <?php if (!empty($person['preferred_name'])): ?>
                                <span class="table__subtext"><?= $e($person['preferred_name']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="E-Mail"><?= $e($person['login_email'] ?? $person['email'] ?? '') ?></td>
                        <td data-label="Status">
                            <span class="<?= $statusBadge($person['status'] ?? '') ?>"><?= $e($person['status'] ?? '') ?></span>
                        </td>
                        <td data-label="Aktionen" class="table__cell--actions">
                            <div class="btn-group btn-group--gap-xs">
                                <a class="btn btn--xs btn--outline" href="/verwaltung/personen/<?= $personId ?>">Öffnen</a>
                                <a class="btn btn--xs btn--ghost" href="/verwaltung/personen/<?= $personId ?>/edit">Bearbeiten</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
