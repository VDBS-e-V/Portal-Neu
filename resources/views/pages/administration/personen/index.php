<?php $e = static fn (mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); $filters = $filters ?? []; ?>
<section class="content-card">
    <h1>Personen-Gruppen</h1>
    <p>Gruppen werden am Subject der Person vergeben.</p>
    <form method="get" class="inline-form">
        <label>Suche <input name="q" value="<?= $e($filters['q'] ?? '') ?>" placeholder="Name, E-Mail oder ID"></label>
        <button class="button" type="submit">Suchen</button>
    </form>
</section>
<section class="content-card">
    <table class="data-table">
        <thead><tr><th>ID</th><th>Name</th><th>E-Mail</th><th>Subject</th><th>Gruppen</th><th></th></tr></thead>
        <tbody>
        <?php foreach (($persons ?? []) as $person): ?>
            <tr>
                <td><?= $e($person['id']) ?></td>
                <td><?= $e($person['display_name'] ?? '') ?></td>
                <td><?= $e($person['email'] ?? '') ?></td>
                <td><code><?= $e($person['subject_uuid'] ?? '—') ?></code></td>
                <td><?= $e($person['active_group_count'] ?? 0) ?></td>
                <td><a href="/administration/personen/<?= $e($person['id']) ?>/gruppen">Gruppen</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (($persons ?? []) === []): ?><tr><td colspan="6">Keine Personen gefunden.</td></tr><?php endif; ?>
        </tbody>
    </table>
</section>
