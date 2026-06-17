<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$person = $person ?? [];
$errors = $errors ?? [];
$mode = (string) ($mode ?? 'create');
$action = (string) ($action ?? '/verwaltung/personen/create');

$value = static function (array $person, string $key, string $default = ''): string {
    return (string) ($person[$key] ?? $default);
};
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $mode === 'edit' ? 'Person bearbeiten' : 'Person anlegen' ?></h1>
            <p>Personenstammdaten und optionalen Login-Zugang pflegen.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen">Zurück</a>
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

    <form method="post" action="<?= $e($action) ?>" class="stack-form">
        <section class="card">
            <h2>Basis</h2>

            <label>
                Anzeigename
                <input type="text" name="display_name" value="<?= $e($value($person, 'display_name')) ?>">
            </label>

            <label>
                Status
                <?php $status = $value($person, 'status', 'active'); ?>
                <select name="status">
                    <?php foreach (['active' => 'Aktiv', 'disabled' => 'Deaktiviert', 'erasure_requested' => 'Löschung beantragt', 'erased' => 'Gelöscht/anonymisiert'] as $statusValue => $label): ?>
                        <option value="<?= $e($statusValue) ?>" <?= $status === $statusValue ? 'selected' : '' ?>>
                            <?= $e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </section>

        <section class="card">
            <h2>Name</h2>

            <label>
                Anrede
                <input type="text" name="salutation" value="<?= $e($value($person, 'salutation')) ?>">
            </label>

            <label>
                Titel
                <input type="text" name="title" value="<?= $e($value($person, 'title')) ?>">
            </label>

            <label>
                Vorname
                <input type="text" name="first_name" value="<?= $e($value($person, 'first_name')) ?>">
            </label>

            <label>
                Zweitname
                <input type="text" name="middle_name" value="<?= $e($value($person, 'middle_name')) ?>">
            </label>

            <label>
                Nachname
                <input type="text" name="last_name" value="<?= $e($value($person, 'last_name')) ?>">
            </label>

            <label>
                Bevorzugter Name
                <input type="text" name="preferred_name" value="<?= $e($value($person, 'preferred_name')) ?>">
            </label>

            <label>
                Pronomen
                <input type="text" name="pronouns" value="<?= $e($value($person, 'pronouns')) ?>">
            </label>
        </section>

        <section class="card">
            <h2>Optionaler Login</h2>

            <p>Login-E-Mail nur ausfüllen, wenn diese Person einen Zugang erhalten soll.</p>

            <label>
                Login-E-Mail
                <input type="email" name="login_email" value="<?= $e($value($person, 'login_email')) ?>">
            </label>

            <?php if ($mode === 'create'): ?>
                <label>
                    Initialpasswort
                    <input type="password" name="login_password" value="">
                </label>
            <?php endif; ?>
        </section>

        <p>
            <button type="submit"><?= $mode === 'edit' ? 'Änderungen speichern' : 'Person anlegen' ?></button>
        </p>
    </form>
</section>