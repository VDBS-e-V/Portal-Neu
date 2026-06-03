<?php declare(strict_types=1); ?>
<section class="areas-list">
    <header>
        <h1><?= htmlspecialchars($pageTitle ?? 'Bereiche', ENT_QUOTES, 'UTF-8') ?></h1>
        <p><a href="/development/web-control/areas/create" class="btn btn--outline btn--md">Neuen Bereich erstellen</a></p>
    </header>

    <?php if (empty($areas)): ?>
        <p>Keine Bereiche gefunden.</p>
    <?php else: ?>
        <table class="vdb-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Öffentlich</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($areas as $area): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($area['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($area['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($area['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($area['is_public']) ? 'Ja' : 'Nein' ?></td>
                    <td>
                        <a class="btn btn--outline btn--sm" href="/development/web-control/areas/edit?id=<?= urlencode((string)($area['id'] ?? '')) ?>">Bearbeiten</a>
                        <form method="post" action="/development/web-control/areas/delete" style="display:inline">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)($area['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" />
                            <button class="btn btn--ghost btn--sm" type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
