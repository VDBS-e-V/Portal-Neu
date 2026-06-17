<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$errors = $errors ?? [];
$csrfToken = (string) ($csrfToken ?? '');
?>

<main class="auth-page">
    <section class="auth-card">
        <h1>Passwort vergessen</h1>

        <p>Gib deine Login-E-Mail-Adresse ein. Wenn ein Konto existiert, wird ein Reset-Link erzeugt.</p>

        <?php if ($errors !== []): ?>
            <div class="notice notice-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/passwort/vergessen" class="stack-form">
            <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">

            <label>
                E-Mail
                <input type="email" name="email" required>
            </label>

            <button type="submit">Reset-Link anfordern</button>
        </form>
    </section>
</main>
