<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$person = $person ?? [];
$errors = $errors ?? [];
$personId = (int) ($person['id'] ?? 0);
$label = trim((string) ($person['display_name'] ?? ''));

if ($label === '') {
    $label = trim((string) ($person['login_email'] ?? 'Person #' . $personId));
}
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>DSGVO-Löschung beantragen</h1>
            <p><?= $e($label) ?></p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen/<?= $personId ?>">Zurück zur Person</a>
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

    <section class="card danger-zone">
        <h2>Wichtiger Hinweis</h2>
        <p>
            Dieser Vorgang beantragt eine echte DSGVO-Löschung/Anonymisierung.
            Die Umsetzung muss danach separat freigegeben und abgeschlossen werden.
        </p>
    </section>

    <form method="post" action="/verwaltung/personen/<?= $personId ?>/datenschutz/loeschung" class="stack-form">
        <section class="card">
            <h2>Löschersuchen</h2>

            <label>
                Grund / Beschreibung
                <textarea name="reason" rows="6" required></textarea>
            </label>
        </section>

        <p>
            <button type="submit">DSGVO-Löschung beantragen</button>
        </p>
    </form>
</section>
