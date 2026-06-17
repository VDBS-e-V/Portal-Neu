<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$token = (string) ($token ?? '');
$reset = $reset ?? [];
$errors = $errors ?? [];
$csrfToken = (string) ($csrfToken ?? '');

$label = trim((string) ($reset['person_display_name'] ?? ''));

if ($label === '') {
    $label = trim((string) ($reset['user_email'] ?? $reset['email'] ?? ''));
}
?>

<main class="auth-page">
    <section class="auth-card">
        <h1>Passwort neu setzen</h1>

        <?php if ($label !== ''): ?>
            <p><?= $e($label) ?></p>
        <?php endif; ?>

        <?php if ($errors !== []): ?>
            <div class="notice notice-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/passwort/zuruecksetzen/<?= $e(rawurlencode($token)) ?>" class="stack-form">
            <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">

            <label>
                Neues Passwort
                <input type="password" name="password" minlength="8" required>
            </label>

            <label>
                Passwort wiederholen
                <input type="password" name="password_repeat" minlength="8" required>
            </label>

            <button type="submit">Passwort speichern</button>
        </form>
    </section>
</main>
