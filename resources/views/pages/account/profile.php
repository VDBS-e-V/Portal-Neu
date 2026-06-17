<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$profile = $profile ?? [];
$contacts = $contacts ?? [];
$addresses = $addresses ?? [];

$label = trim((string) ($profile['display_name'] ?? ''));

if ($label === '') {
    $label = trim((string) ($profile['preferred_name'] ?? ''));
}

if ($label === '') {
    $label = trim(trim((string) ($profile['first_name'] ?? '')) . ' ' . trim((string) ($profile['last_name'] ?? '')));
}

if ($label === '') {
    $label = trim((string) ($profile['login_email'] ?? 'Mein Profil'));
}
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Mein Profil</h1>
            <p><?= $e($label) ?></p>
        </div>

        <p>
            <a class="button button-primary" href="/konto/profil/bearbeiten">Profil bearbeiten</a>
        </p>
    </header>

    <nav class="tabs">
        <a href="/konto/profil">Profil</a>
        <a href="/konto/kontakte">Kontakte</a>
        <a href="/konto/adressen">Adressen</a>
        <a href="/konto/passwort">Passwort</a>
        <a href="/konto/sicherheit">Sicherheit</a>
    </nav>

    <section class="card">
        <h2>Person</h2>

        <dl>
            <dt>Anzeigename</dt>
            <dd><?= $e($profile['display_name'] ?? '') ?></dd>

            <dt>Name</dt>
            <dd>
                <?= $e(trim(trim((string) ($profile['first_name'] ?? '')) . ' ' . trim((string) ($profile['last_name'] ?? '')))) ?>
            </dd>

            <dt>Bevorzugter Name</dt>
            <dd><?= $e($profile['preferred_name'] ?? '') ?></dd>

            <dt>Pronomen</dt>
            <dd><?= $e($profile['pronouns'] ?? '') ?></dd>

            <dt>Status</dt>
            <dd><?= $e($profile['person_status'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Login</h2>

        <dl>
            <dt>E-Mail</dt>
            <dd><?= $e($profile['login_email'] ?? '') ?></dd>

            <dt>Status</dt>
            <dd><?= $e($profile['login_status'] ?? '') ?></dd>

            <dt>Letzter Login</dt>
            <dd><?= $e($profile['last_login_at'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="content-grid">
        <article class="card">
            <h2>Kontakte</h2>
            <p><?= count($contacts) ?> Kontakt(e)</p>
            <p><a href="/konto/kontakte">Kontakte verwalten</a></p>
        </article>

        <article class="card">
            <h2>Adressen</h2>
            <p><?= count($addresses) ?> Adresse(n)</p>
            <p><a href="/konto/adressen">Adressen verwalten</a></p>
        </article>
    </section>
</section>
