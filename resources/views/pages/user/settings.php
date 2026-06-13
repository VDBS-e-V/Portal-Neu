<?php
$user = is_array($user ?? null) ? $user : [];
$settings = is_array($settings ?? null) ? $settings : [];
$csrfToken = (string) ($csrfToken ?? '');
$saved = (bool) ($saved ?? false);
$passwordChanged = (bool) ($passwordChanged ?? false);
$passwordError = $passwordError ?? null;

$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="section--surface">
    <div class="container container--text-only container--narrow">
        <div class="form form--card">
            <div class="form__grid form__grid--1">
                <div class="form__title">
                    <h1>Kontoeinstellungen</h1>
                    <p>Verwalten Sie Sprache, Benachrichtigungen, Sichtbarkeit und Passwort.</p>
                </div>

                <?php if ($saved): ?>
                    <div class="form__notice form__notice--success" role="status">
                        <strong>Gespeichert:</strong>
                        Die Kontoeinstellungen wurden aktualisiert.
                    </div>
                <?php endif; ?>

                <?php if ($passwordChanged): ?>
                    <div class="form__notice form__notice--success" role="status">
                        <strong>Gespeichert:</strong>
                        Das Passwort wurde geändert.
                    </div>
                <?php endif; ?>

                <?php if (is_string($passwordError) && $passwordError !== ''): ?>
                    <div class="form__notice form__notice--error" role="alert">
                        <strong>Fehler:</strong>
                        <?= $e($passwordError) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <form method="post" action="/user/settings" class="form form--card">
            <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">

            <div class="form__grid form__grid--2">
                <div class="form__section-title form__section-title--primary">
                    <h2>Allgemeine Einstellungen</h2>
                    <p>Diese Einstellungen steuern die Nutzung des Portals.</p>
                </div>

                <div class="form__field form__field--span-full">
                    <label class="form__label" for="email">Login-E-Mail</label>
                    <input class="form__control" id="email" type="email" value="<?= $e($user['email'] ?? '') ?>" readonly>
                </div>

                <div class="form__field">
                    <label class="form__label" for="language">Sprache</label>
                    <select class="form__control" id="language" name="language">
                        <option value="de" <?= ($settings['language'] ?? 'de') === 'de' ? 'selected' : '' ?>>Deutsch</option>
                        <option value="en" <?= ($settings['language'] ?? 'de') === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="timezone">Zeitzone</label>
                    <input class="form__control" id="timezone" name="timezone" type="text" value="<?= $e($settings['timezone'] ?? 'Europe/Berlin') ?>">
                </div>

                <div class="form__field">
                    <label class="form__label" for="profile_visibility">Profilsichtbarkeit</label>
                    <select class="form__control" id="profile_visibility" name="profile_visibility">
                        <option value="private" <?= ($settings['profile_visibility'] ?? 'private') === 'private' ? 'selected' : '' ?>>Privat</option>
                        <option value="members" <?= ($settings['profile_visibility'] ?? 'private') === 'members' ? 'selected' : '' ?>>Nur Mitglieder</option>
                        <option value="public" <?= ($settings['profile_visibility'] ?? 'private') === 'public' ? 'selected' : '' ?>>Öffentlich</option>
                    </select>
                </div>

                <div class="form__field">
                    <div class="form__switch">
                        <input
                            class="form__switch-input"
                            id="email_notifications"
                            name="email_notifications"
                            type="checkbox"
                            value="1"
                            <?= (int) ($settings['email_notifications'] ?? 1) === 1 ? 'checked' : '' ?>
                        >

                        <label class="form__switch-label" for="email_notifications">
                            E-Mail-Benachrichtigungen aktivieren
                        </label>
                    </div>
                </div>

                <div class="form__actions form__actions--between">
                    <a class="btn btn--primary-transp btn--md" href="/user">
                        Zurück zum Profil
                    </a>

                    <button class="btn btn--primary btn--md" type="submit">
                        Einstellungen speichern
                    </button>
                </div>
            </div>
        </form>

        <form method="post" action="/user/password" class="form form--card">
            <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">

            <div class="form__grid form__grid--1">
                <div class="form__section-title form__section-title--secondary-cta">
                    <h2>Passwort ändern</h2>
                    <p>Das neue Passwort muss mindestens 10 Zeichen lang sein.</p>
                </div>

                <div class="form__field">
                    <label class="form__label" for="current_password">Aktuelles Passwort</label>
                    <input class="form__control" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                </div>

                <div class="form__field">
                    <label class="form__label" for="new_password">Neues Passwort</label>
                    <input class="form__control" id="new_password" name="new_password" type="password" autocomplete="new-password" required>
                </div>

                <div class="form__field">
                    <label class="form__label" for="new_password_repeat">Neues Passwort wiederholen</label>
                    <input class="form__control" id="new_password_repeat" name="new_password_repeat" type="password" autocomplete="new-password" required>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary btn--md" type="submit">
                        Passwort ändern
                    </button>
                </div>
            </div>
        </form>

        <form method="post" action="/logout" class="form form--card">
            <input type="hidden" name="_csrf" value="<?= $e($csrfToken) ?>">

            <div class="form__grid form__grid--1">
                <div class="form__section-title form__section-title--secondary-highlight">
                    <h2>Sitzung</h2>
                    <p>Hier können Sie sich vom Portal abmelden.</p>
                </div>

                <div class="form__actions form__actions--right">
                    <button class="btn btn--primary-transp btn--md" type="submit">
                        Abmelden
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>