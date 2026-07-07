<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$settings = is_array($settings ?? null) ? $settings : [];
$user = is_array($user ?? null) ? $user : [];
$profileUrl = (string) ($canonicalProfileUrl ?? '/konto');
$settingsUrl = (string) ($canonicalSettingsUrl ?? '/konto/einstellungen');
?>

<section class="page-section konto-settings-page">
    <div class="container">
        <header class="page-header">
            <p class="text-muted">Konto</p>
            <h1>Kontoeinstellungen</h1>
            <p>Verwalten Sie Sprache, Benachrichtigungen, Sichtbarkeit und Passwort.</p>
            <nav class="button-row" aria-label="Kontonavigation">
                <a class="button button-secondary" href="<?= $e($profileUrl) ?>">Zurück zum Profil</a>
            </nav>
        </header>

        <?php if (!empty($saved)): ?>
            <div class="alert alert-success">Die Kontoeinstellungen wurden gespeichert.</div>
        <?php endif; ?>

        <?php if (!empty($passwordChanged)): ?>
            <div class="alert alert-success">Das Passwort wurde geändert.</div>
        <?php endif; ?>

        <?php if (!empty($passwordError)): ?>
            <div class="alert alert-danger"><?= $e($passwordError) ?></div>
        <?php endif; ?>

        <section class="card">
            <h2>Allgemeine Einstellungen</h2>
            <form method="post" action="<?= $e($settingsUrl) ?>" class="form-card">
                <input type="hidden" name="_csrf" value="<?= $e($csrfToken ?? '') ?>">

                <div class="form-grid">
                    <label>
                        <span>Login-E-Mail</span>
                        <input type="email" value="<?= $e($user['email'] ?? '') ?>" readonly>
                    </label>
                    <label>
                        <span>Sprache</span>
                        <select name="language">
                            <?php foreach (['de' => 'Deutsch', 'en' => 'English'] as $value => $label): ?>
                                <option value="<?= $e($value) ?>" <?= (($settings['language'] ?? 'de') === $value) ? 'selected' : '' ?>><?= $e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <span>Zeitzone</span>
                        <input type="text" name="timezone" value="<?= $e($settings['timezone'] ?? 'Europe/Berlin') ?>">
                    </label>
                    <label>
                        <span>Profilsichtbarkeit</span>
                        <select name="profile_visibility">
                            <?php foreach (['private' => 'Privat', 'members' => 'Nur Mitglieder', 'public' => 'Öffentlich'] as $value => $label): ?>
                                <option value="<?= $e($value) ?>" <?= (($settings['profile_visibility'] ?? 'private') === $value) ? 'selected' : '' ?>><?= $e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>
                        <input type="checkbox" name="email_notifications" value="1" <?= !empty($settings['email_notifications']) ? 'checked' : '' ?>>
                        <span>E-Mail-Benachrichtigungen aktivieren</span>
                    </label>
                </div>

                <div class="button-row">
                    <button type="submit" class="button button-primary">Einstellungen speichern</button>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Passwort ändern</h2>
            <p>Das neue Passwort muss mindestens 10 Zeichen lang sein.</p>
            <form method="post" action="/konto/passwort" class="form-card">
                <input type="hidden" name="_csrf" value="<?= $e($csrfToken ?? '') ?>">

                <div class="form-grid">
                    <label>
                        <span>Aktuelles Passwort</span>
                        <input type="password" name="current_password" autocomplete="current-password" required>
                    </label>
                    <label>
                        <span>Neues Passwort</span>
                        <input type="password" name="new_password" autocomplete="new-password" required>
                    </label>
                    <label>
                        <span>Neues Passwort wiederholen</span>
                        <input type="password" name="new_password_repeat" autocomplete="new-password" required>
                    </label>
                </div>

                <div class="button-row">
                    <button type="submit" class="button button-primary">Passwort ändern</button>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Sitzung</h2>
            <form method="post" action="/logout">
                <button type="submit" class="button button-secondary">Abmelden</button>
            </form>
        </section>
    </div>
</section>
