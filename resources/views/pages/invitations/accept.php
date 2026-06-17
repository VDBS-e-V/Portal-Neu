<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$token = (string) ($token ?? '');
$invitation = $invitation ?? [];
$errors = $errors ?? [];

$label = trim((string) ($invitation['person_display_name'] ?? ''));
if ($label === '') {
    $label = trim((string) ($invitation['user_email'] ?? $invitation['email'] ?? ''));
}
?>

<main class="auth-page">
    <section class="auth-card">
        <h1>Einladung annehmen</h1>

        <p>
            Willkommen<?= $label !== '' ? ', ' . $e($label) : '' ?>.
            Bitte lege ein Passwort für deinen Zugang fest.
        </p>

        <?php if ($errors !== []): ?>
            <div class="notice notice-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/einladung/<?= $e(rawurlencode($token)) ?>" class="stack-form">
            <label>
                E-Mail
                <input type="email" value="<?= $e($invitation['user_email'] ?? $invitation['email'] ?? '') ?>" readonly>
            </label>

            <label>
                Passwort
                <input type="password" name="password" minlength="8" required>
            </label>

            <label>
                Passwort wiederholen
                <input type="password" name="password_repeat" minlength="8" required>
            </label>

            <button type="submit">Einladung annehmen</button>
        </form>
    </section>
</main>
