<?php declare(strict_types=1); ?>
<section class="menus-list">
    <header>
        <h1><?= htmlspecialchars($pageTitle ?? 'Menüs', ENT_QUOTES, 'UTF-8') ?></h1>
        <p><a href="/development/web-control/menus/create" class="btn btn--outline btn--md">Neues Menü erstellen</a></p>
    </header>

    <?php if (empty($menus)): ?>
        <p>Keine Menüs gefunden.</p>
    <?php else: ?>
        <table class="vdb-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Area</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($menu['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($areasMap[$menu['area_id']] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($menu['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($menu['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a class="btn btn--outline btn--sm" href="/development/web-control/menus/edit?id=<?= urlencode((string) ($menu['id'] ?? '')) ?>">Bearbeiten</a>
                        <form method="post" action="/development/web-control/menus/delete" style="display:inline">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($menu['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" />
                            <button class="btn btn--ghost btn--sm" type="submit">Löschen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
