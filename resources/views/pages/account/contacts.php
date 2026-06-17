<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$contacts = $contacts ?? [];
$errors = $errors ?? [];
$message = (string) ($message ?? '');
$csrfToken = (string) ($csrfToken ?? '');
$deleteTokens = $deleteTokens ?? [];
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Meine Kontakte</h1>
            <p>Eigene Kontaktmöglichkeiten pflegen.</p>
        </div>

        <p>
            <a class="button" href="/konto/profil">Zurück zum Profil</a>
        </p>
    </header>

    <nav class="tabs">
        <a href="/konto/profil">Profil</a>
        <a href="/konto/kontakte">Kontakte</a>
        <a href="/konto/adressen">Adressen</a>
        <a href="/konto/passwort">Passwort</a>
        <a href="/konto/sicherheit">Sicherheit</a>
    </nav>

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

    <section class="card">
        <h2>Kontakt hinzufügen</h2>

        <form method="post" action="/konto/kontakte" class="stack-form">
            <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">

            <label>
                Typ
                <select name="contact_type">
                    <option value="email">E-Mail</option>
                    <option value="phone">Telefon</option>
                    <option value="mobile">Mobil</option>
                    <option value="website">Website</option>
                    <option value="other">Sonstiges</option>
                </select>
            </label>

            <label>Label <input type="text" name="label" placeholder="privat, dienstlich ..."></label>
            <label>Wert <input type="text" name="value" required></label>
            <label><input type="checkbox" name="is_primary" value="1"> Primär</label>

            <button type="submit">Kontakt speichern</button>
        </form>
    </section>

    <section class="card">
        <h2>Kontakte</h2>

        <table class="data-table">
            <thead>
            <tr>
                <th>Typ</th>
                <th>Label</th>
                <th>Wert</th>
                <th>Primär</th>
                <th>Verifiziert</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($contacts === []): ?>
                <tr><td colspan="6">Keine Kontakte vorhanden.</td></tr>
            <?php endif; ?>

            <?php foreach ($contacts as $contact): ?>
                <?php $contactId = (int) ($contact['id'] ?? 0); ?>
                <tr>
                    <td><?= $e($contact['contact_type'] ?? '') ?></td>
                    <td><?= $e($contact['label'] ?? '') ?></td>
                    <td><?= $e($contact['value'] ?? '') ?></td>
                    <td><?= !empty($contact['is_primary']) ? 'ja' : 'nein' ?></td>
                    <td><?= !empty($contact['is_verified']) ? 'ja' : 'nein' ?></td>
                    <td>
                        <form method="post" action="/konto/kontakte/<?= $contactId ?>/delete">
                            <input type="hidden" name="_csrf_token" value="<?= $e($deleteTokens[$contactId] ?? '') ?>">
                            <button type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
