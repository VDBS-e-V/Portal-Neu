<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$user = $user ?? [];

$label = trim((string) ($user['person_display_name'] ?? ''));

if ($label === '') {
    $label = trim((string) ($user['preferred_name'] ?? ''));
}

if ($label === '') {
    $label = trim(trim((string) ($user['first_name'] ?? '')) . ' ' . trim((string) ($user['last_name'] ?? '')));
}

if ($label === '') {
    $label = trim((string) ($user['email'] ?? 'Mein Konto'));
}
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Mein Konto</h1>
            <p><?= $e($label) ?></p>
        </div>
    </header>

    <section class="card">
        <h2>Login</h2>

        <dl>
            <dt>User-ID</dt>
            <dd><?= (int) ($user['id'] ?? 0) ?></dd>

            <dt>E-Mail</dt>
            <dd><?= $e($user['email'] ?? '') ?></dd>

            <dt>Status</dt>
            <dd><?= $e($user['status'] ?? '') ?></dd>

            <dt>Person</dt>
            <dd><?= $e($user['person_display_name'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Sicherheit</h2>

        <p>
            <a class="button button-primary" href="/konto/passwort">Passwort ändern</a>
        </p>
    </section>
</section>
