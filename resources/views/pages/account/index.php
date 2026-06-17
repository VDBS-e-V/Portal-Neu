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
$user = $user ?? [];
$label = trim((string) ($user['person_display_name'] ?? $user['preferred_name'] ?? ''));
if ($label === '') {
    $label = trim(trim((string) ($user['first_name'] ?? '')) . ' ' . trim((string) ($user['last_name'] ?? '')));
}
if ($label === '') {
    $label = (string) ($user['email'] ?? 'Mein Konto');
}
?>

<section class="section--page-title section--page-title-compact">
    <div class="page-title page-title--card page-title--split page-title--compact">
        <div class="page-title__main">
            <p class="page-title__kicker">Konto</p>
            <h1 class="page-title__title">Mein Konto</h1>
            <p class="page-title__lead">Persönliche Daten, Passwort und Sicherheit verwalten.</p>
        </div>
        
    </div>
</section>

<section>
    <div class="grid">
        <article class="summary-card summary-card--primary">
            <p class="summary-card__kicker">Login</p>
            <h2 class="summary-card__title"><?= $e($user['email'] ?? '') ?></h2>
            <p class="summary-card__text"><?= $e($label) ?></p>
        </article>

        <article class="summary-card summary-card--info">
            <p class="summary-card__kicker">Status</p>
            <h2 class="summary-card__title"><?= $e($user['status'] ?? '') ?></h2>
            <p class="summary-card__text">User-ID: <?= (int) ($user['id'] ?? 0) ?></p>
        </article>
    </div>
</section>

<section>
    <div class="summary-group summary-group--card">
        <div class="summary-group__header">
            <p class="summary-group__kicker">Konto</p>
            <h2 class="summary-group__title">Aktionen</h2>
        </div>
        <div class="btn-group btn-group--gap-sm">
            <a class="btn btn--primary" href="/konto/profil">Profil anzeigen</a>
            <a class="btn btn--outline" href="/konto/kontakte">Kontakte</a>
            <a class="btn btn--outline" href="/konto/adressen">Adressen</a>
            <a class="btn btn--outline" href="/konto/passwort">Passwort ändern</a>
            <a class="btn btn--outline" href="/konto/sicherheit">Sicherheit</a>
        </div>
    </div>
</section>
