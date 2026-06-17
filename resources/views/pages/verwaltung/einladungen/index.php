<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$invitations = $invitations ?? [];
$message = (string) ($message ?? '');
$lastInvitationUrl = (string) ($lastInvitationUrl ?? '');
?>

<section class="content-section">
    <header class="content-header">
        <div>
            <h1>Einladungen</h1>
            <p>Login-Einladungen erstellen, prüfen und widerrufen.</p>
        </div>

        <p>
            <a class="button" href="/verwaltung/personen">Personen verwalten</a>
        </p>
    </header>

    <?php if ($message !== ''): ?>
        <div class="notice notice-success"><?= $e($message) ?></div>
    <?php endif; ?>

    <?php if ($lastInvitationUrl !== ''): ?>
        <section class="card">
            <h2>Neue Einladung</h2>
            <p>Der Einladungslink wurde erzeugt. Kopiere ihn und versende ihn manuell:</p>
            <pre><?= $e($lastInvitationUrl) ?></pre>
        </section>
    <?php endif; ?>

    <section class="card">
        <h2>Letzte Einladungen</h2>

        <table class="data-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Person/Login</th>
                <th>Status</th>
                <th>Läuft ab</th>
                <th>Erstellt</th>
                <th>Angenommen</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($invitations === []): ?>
                <tr>
                    <td colspan="7">Keine Einladungen vorhanden.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($invitations as $invitation): ?>
                <?php $invitationId = (int) ($invitation['id'] ?? 0); ?>
                <tr>
                    <td><?= $invitationId ?></td>
                    <td>
                        <?= $e($invitation['person_display_name'] ?? '') ?>
                        <?php if (!empty($invitation['user_email'] ?? $invitation['email'] ?? '')): ?>
                            <br><?= $e($invitation['user_email'] ?? $invitation['email']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $e($invitation['status'] ?? '') ?></td>
                    <td><?= $e($invitation['expires_at'] ?? '') ?></td>
                    <td><?= $e($invitation['created_at'] ?? '') ?></td>
                    <td><?= $e($invitation['accepted_at'] ?? '') ?></td>
                    <td>
                        <?php if (($invitation['status'] ?? '') === 'pending'): ?>
                            <form method="post" action="/verwaltung/einladungen/<?= $invitationId ?>/revoke">
                                <button type="submit">Widerrufen</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
