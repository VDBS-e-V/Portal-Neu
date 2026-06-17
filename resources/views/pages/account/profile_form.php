<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$profile = $profile ?? [];
$errors = $errors ?? [];
$csrfToken = (string) ($csrfToken ?? '');

$value = static fn (array $profile, string $key, string $default = ''): string => (string) ($profile[$key] ?? $default);
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Profil bearbeiten</h1>
            <p>Eigene Stammdaten pflegen.</p>
        </div>

        <p>
            <a class="button" href="/konto/profil">Zurück zum Profil</a>
        </p>
    </header>

    <?php if ($errors !== []): ?>
        <div class="notice notice-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/konto/profil/bearbeiten" class="stack-form">
        <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">

        <section class="card">
            <h2>Basis</h2>

            <label>
                Anzeigename
                <input type="text" name="display_name" value="<?= $e($value($profile, 'display_name')) ?>">
            </label>

            <label>Anrede <input type="text" name="salutation" value="<?= $e($value($profile, 'salutation')) ?>"></label>
            <label>Titel <input type="text" name="title" value="<?= $e($value($profile, 'title')) ?>"></label>
            <label>Vorname <input type="text" name="first_name" value="<?= $e($value($profile, 'first_name')) ?>"></label>
            <label>Zweitname <input type="text" name="middle_name" value="<?= $e($value($profile, 'middle_name')) ?>"></label>
            <label>Nachname <input type="text" name="last_name" value="<?= $e($value($profile, 'last_name')) ?>"></label>
            <label>Bevorzugter Name <input type="text" name="preferred_name" value="<?= $e($value($profile, 'preferred_name')) ?>"></label>
            <label>Pronomen <input type="text" name="pronouns" value="<?= $e($value($profile, 'pronouns')) ?>"></label>
        </section>

        <p>
            <button type="submit">Profil speichern</button>
        </p>
    </form>
</section>
