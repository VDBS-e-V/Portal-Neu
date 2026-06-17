<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$errors = $errors ?? [];
$message = (string) ($message ?? '');
$csrfToken = (string) ($csrfToken ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Passwort ändern</h1>
            <p>Ändere dein eigenes Login-Passwort.</p>
        </div>

        <p>
            <a class="button" href="/konto">Zurück zum Konto</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
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

    <form method="post" action="/konto/passwort" class="stack-form">
        <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">

        <section class="card">
            <h2>Passwort</h2>

            <label>
                Aktuelles Passwort
                <input type="password" name="current_password" required>
            </label>

            <label>
                Neues Passwort
                <input type="password" name="new_password" minlength="8" required>
            </label>

            <label>
                Neues Passwort wiederholen
                <input type="password" name="new_password_repeat" minlength="8" required>
            </label>

            <p>
                Das Passwort muss mindestens 8 Zeichen, einen Großbuchstaben, einen Kleinbuchstaben und eine Zahl enthalten.
            </p>
        </section>

        <p>
            <button type="submit">Passwort ändern</button>
        </p>
    </form>
</section>
