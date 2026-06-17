<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$resetUrl = (string) ($resetUrl ?? '');
?>

<main class="auth-page">
    <section class="auth-card">
        <h1>Reset angefordert</h1>

        <p>Wenn ein Konto mit dieser E-Mail-Adresse existiert, wurde ein Passwort-Reset vorbereitet.</p>

        <?php if ($resetUrl !== ''): ?>
            <section class="card">
                <h2>Entwicklungsmodus</h2>
                <p>Bis ein Mailer eingebaut ist, kann dieser Link manuell kopiert werden:</p>
                <pre><?= $e($resetUrl) ?></pre>
            </section>
        <?php endif; ?>

        <p>
            <a class="button" href="/login">Zum Login</a>
        </p>
    </section>
</main>
