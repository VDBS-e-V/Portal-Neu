<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$user = $user ?? [];
$events = $events ?? [];
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Sicherheit</h1>
            <p>Login- und Sicherheitsereignisse deines Kontos.</p>
        </div>

        <p>
            <a class="button" href="/konto/passwort">Passwort ändern</a>
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
        <h2>Account</h2>

        <dl>
            <dt>E-Mail</dt>
            <dd><?= $e($user['email'] ?? '') ?></dd>

            <dt>Status</dt>
            <dd><?= $e($user['status'] ?? '') ?></dd>
        </dl>
    </section>

    <section class="card">
        <h2>Letzte Ereignisse</h2>

        <table class="data-table">
            <thead>
            <tr>
                <th>Zeitpunkt</th>
                <th>Ereignis</th>
                <th>IP</th>
                <th>Request</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($events === []): ?>
                <tr>
                    <td colspan="4">Keine Ereignisse vorhanden.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= $e($event['occurred_at'] ?? '') ?></td>
                    <td><code><?= $e($event['event_type'] ?? '') ?></code></td>
                    <td><?= $e($event['ip_address'] ?? '') ?></td>
                    <td><?= $e($event['request_uri'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
