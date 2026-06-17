<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$group = $group ?? [];
$errors = $errors ?? [];
$mode = (string) ($mode ?? 'create');
$action = (string) ($action ?? '/verwaltung/gruppen/create');
$isSystem = !empty($group['is_system']);

$value = static function (array $group, string $key, string $default = ''): string {
    return (string) ($group[$key] ?? $default);
};
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1><?= $mode === 'edit' ? 'Gruppe bearbeiten' : 'Gruppe anlegen' ?></h1>
            <p>Gruppenschlüssel, Name und Beschreibung pflegen.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/gruppen">Zurück</a>
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

    <?php if ($isSystem): ?>
        <div class="notice notice-info">
            Diese Gruppe ist eine Systemgruppe. Der technische Schlüssel und der Systemstatus sind geschützt.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $e($action) ?>" class="stack-form">
        <section class="card">
            <h2>Basis</h2>

            <label>
                Gruppenschlüssel
                <input
                    type="text"
                    name="group_key"
                    value="<?= $e($value($group, 'group_key')) ?>"
                    <?= $isSystem ? 'readonly' : '' ?>
                    required
                >
            </label>

            <label>
                Name
                <input type="text" name="name" value="<?= $e($value($group, 'name')) ?>" required>
            </label>

            <label>
                Beschreibung
                <textarea name="description" rows="5"><?= $e($value($group, 'description')) ?></textarea>
            </label>

            <?php if (!$isSystem): ?>
                <label>
                    <input type="checkbox" name="is_system" value="1" <?= !empty($group['is_system']) ? 'checked' : '' ?>>
                    Systemgruppe
                </label>
            <?php endif; ?>
        </section>

        <p>
            <button type="submit"><?= $mode === 'edit' ? 'Änderungen speichern' : 'Gruppe anlegen' ?></button>
        </p>
    </form>
</section>