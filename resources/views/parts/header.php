<?php declare(strict_types=1); ?>
<?php
$headerMenuItems = is_array($headerMenus ?? null) ? $headerMenus : [];

$isLoggedIn = (bool) ($isLoggedIn ?? false);
$currentUser = is_array($currentUser ?? null) ? $currentUser : [];
$csrfToken = (string) ($csrfToken ?? '');

$headerEscape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$currentUserDisplayName = trim((string) ($currentUser['display_name'] ?? ''));

if ($currentUserDisplayName === '') {
    $currentUserDisplayName = trim((string) ($currentUser['email'] ?? 'Benutzerkonto'));
}

$currentUserUsername = trim((string) ($currentUser['username'] ?? ''));

if ($currentUserUsername === '') {
    $currentUserUsername = trim((string) ($currentUser['email'] ?? ''));
}

$currentUserEmail = trim((string) ($currentUser['email'] ?? ''));
$currentUserInitials = trim((string) ($currentUser['initials'] ?? 'U'));
$currentUserAvatarPath = trim((string) ($currentUser['avatar_path'] ?? ''));

if ($currentUserAvatarPath === '') {
    $currentUserAvatarPath = '/assets/images/avatars/default.jpg';
}

$getHeaderMenuChildren = static function (array $item): array {
    foreach (['children', 'items', 'sub_items', 'submenu', 'submenus'] as $childrenKey) {
        if (!empty($item[$childrenKey]) && is_array($item[$childrenKey])) {
            return array_values(array_filter($item[$childrenKey], 'is_array'));
        }
    }

    return [];
};

$normalizeHeaderMenuId = static function (array $item, int $index): string {
    foreach (['id', 'menu_item_id', 'item_id'] as $idKey) {
        if (isset($item[$idKey]) && (string) $item[$idKey] !== '') {
            return (string) $item[$idKey];
        }
    }

    if (isset($item['slug']) && (string) $item['slug'] !== '') {
        return 'slug:' . (string) $item['slug'];
    }

    return 'index:' . $index;
};

$buildHeaderMenuTree = static function (array $items) use ($getHeaderMenuChildren, $normalizeHeaderMenuId): array {
    $hasParentReferences = false;

    foreach ($items as $item) {
        if (
            is_array($item)
            && isset($item['parent_id'])
            && (string) $item['parent_id'] !== ''
            && (string) $item['parent_id'] !== '0'
        ) {
            $hasParentReferences = true;
            break;
        }
    }

    if (!$hasParentReferences) {
        return array_values(array_filter($items, 'is_array'));
    }

    $byId = [];
    $order = [];

    foreach ($items as $index => $item) {
        if (!is_array($item)) {
            continue;
        }

        $id = $normalizeHeaderMenuId($item, (int) $index);
        $item['_header_menu_id'] = $id;
        $byId[$id] = $item;
        $order[] = $id;
    }

    $childrenByParent = ['__root__' => []];

    foreach ($order as $id) {
        $item = $byId[$id];
        $parentId = isset($item['parent_id']) ? (string) $item['parent_id'] : '';

        if ($parentId === '' || $parentId === '0' || !isset($byId[$parentId])) {
            $parentId = '__root__';
        }

        $childrenByParent[$parentId][] = $id;
    }

    $buildBranch = static function (string $parentId) use (&$buildBranch, &$byId, &$childrenByParent, $getHeaderMenuChildren): array {
        $branch = [];

        foreach (($childrenByParent[$parentId] ?? []) as $childId) {
            $item = $byId[$childId];
            $nestedChildren = $getHeaderMenuChildren($item);
            $flatChildren = $buildBranch($childId);

            if (!empty($nestedChildren) || !empty($flatChildren)) {
                $item['children'] = array_merge($nestedChildren, $flatChildren);
            }

            $branch[] = $item;
        }

        return $branch;
    };

    return $buildBranch('__root__');
};

$headerMenuTree = $buildHeaderMenuTree($headerMenuItems);

$renderHeaderMenuItem = static function (array $mi, int $depth = 1) use (&$renderHeaderMenuItem, $getHeaderMenuChildren): string {
    $url = (string) ($mi['url'] ?? ('/' . ltrim((string) ($mi['slug'] ?? ''), '/')));
    $title = (string) ($mi['title'] ?? ($mi['name'] ?? ''));
    $children = $getHeaderMenuChildren($mi);
    $hasChildren = !empty($children);

    $liClass = $depth === 1 ? 'header-bottom-nav-list-item' : 'header-bottom-submenu-item';
    $linkClass = $depth === 1 ? 'link--no-style header-bottom-nav-link' : 'link--no-style header-bottom-submenu-link';

    if ($hasChildren) {
        $liClass .= ' has-submenu';
    }

    if (!empty($mi['active']) || !empty($mi['is_current']) || !empty($mi['current'])) {
        $liClass .= ' active';
    }

    $html = '<li class="' . htmlspecialchars($liClass, ENT_QUOTES, 'UTF-8') . '">';
    $html .= '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($linkClass, ENT_QUOTES, 'UTF-8') . '"';

    if ($hasChildren) {
        $html .= ' aria-haspopup="true" aria-expanded="false"';
    }

    $html .= '><span>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span></a>';

    if ($hasChildren) {
        $submenuClass = $depth === 1 ? 'header-bottom-submenu' : 'header-bottom-submenu header-bottom-submenu--nested';
        $html .= '<ul class="' . htmlspecialchars($submenuClass, ENT_QUOTES, 'UTF-8') . '" aria-label="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">';

        foreach ($children as $child) {
            if (is_array($child)) {
                $html .= $renderHeaderMenuItem($child, $depth + 1);
            }
        }

        $html .= '</ul>';
    }

    $html .= '</li>';

    return $html;
};
?>

<header class="site-header" role="banner">
    <div class="header-top">
        <div class="header-top-logo" onclick="window.location.href='/'" style="cursor: pointer;">
            <img src="/assets/images/bildmarken/bildmarke_breit.png" alt="Logo">
        </div>

        <div class="header-top-areas">
            <ul class="header-top-areas-list">
                <?php if (!empty($headerAreas)): ?>
                    <?php foreach ($headerAreas as $ha): ?>
                        <?php
                            $startPath = (string) ($ha['start_path'] ?? '/');
                            $areaUrl = $startPath !== '' ? $startPath : '/';
                        ?>
                        <li class="header-top-areas-list-item">
                            <a href="<?= $headerEscape($areaUrl) ?>" class="link--no-style">
                                <?= $headerEscape($ha['name'] ?? '') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="header-bottom">
        <div
            class="header-bottom-pagename"
            onclick="window.location.href='<?= $headerEscape($areaRootLink ?? '#') ?>'"
            style="cursor: pointer;"
        >
            <h2 class="area-name"><?= $headerEscape($areaName ?? 'Home') ?></h2>
            <h1 class="page-title"><?= $headerEscape($pageTitle ?? 'Home') ?></h1>
        </div>

        <div class="header-bottom-nav">
            <ul class="header-bottom-nav-list">
                <?php if (!empty($headerMenuTree)): ?>
                    <?php foreach ($headerMenuTree as $mi): ?>
                        <?php if (is_array($mi)): ?>
                            <?= $renderHeaderMenuItem($mi) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="header-bottom-search"></div>

        <div class="header-bottom-user">
            <?php if ($isLoggedIn): ?>
                <button
                    class="header-bottom-user-avatar"
                    popovertarget="user-popover"
                    type="button"
                    aria-label="Benutzermenü öffnen"
                >
                    <img src="<?= $headerEscape($currentUserAvatarPath) ?>" alt="<?= $headerEscape($currentUserDisplayName) ?>">
                </button>

                <div class="header-user-popover" id="user-popover" popover>
                    <div class="popover-header">
                        <div class="popover-avatar">
                            <img src="<?= $headerEscape($currentUserAvatarPath) ?>" alt="<?= $headerEscape($currentUserDisplayName) ?>">
                        </div>

                        <div class="popover-user">
                            <div class="popover-name">
                                <?= $headerEscape($currentUserDisplayName) ?>
                            </div>

                            <div class="popover-username">
                                <svg class="vdb-icon vdb-icon--sm vdb-icon--medium vdb-icon--current" aria-hidden="true">
                                    <use href="/assets/icons/vdb-icons.svg#icon-profile"></use>
                                </svg>

                                <span class="popover-username-text">
                                    <?= $headerEscape($currentUserUsername) ?>
                                </span>
                            </div>

                            <?php if ($currentUserEmail !== '' && $currentUserEmail !== $currentUserUsername): ?>
                                <div class="popover-username">
                                    <svg class="vdb-icon vdb-icon--sm vdb-icon--medium vdb-icon--current" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-mail"></use>
                                    </svg>

                                    <span class="popover-username-text">
                                        <?= $headerEscape($currentUserEmail) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="popover-sep">

                    <ul class="header-user-popover-list">
                        <li class="header-user-popover-list-item">
                            <a href="/user" class="link--no-style">
                                <span class="popover-icon">
                                    <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-user"></use>
                                    </svg>
                                </span>
                                <span>Mein Profil</span>
                            </a>
                        </li>

                        <li class="header-user-popover-list-item">
                            <a href="/user/settings" class="link--no-style">
                                <span class="popover-icon">
                                    <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-settings"></use>
                                    </svg>
                                </span>
                                <span>Kontoeinstellungen</span>
                            </a>
                        </li>

                        <li class="header-user-popover-list-item">
                            <a href="/tickets" class="link--no-style">
                                <span class="popover-icon">
                                    <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-inbox"></use>
                                    </svg>
                                </span>
                                <span>Meine Tickets</span>
                            </a>
                        </li>
                    </ul>

                    <hr class="popover-sep">

                    <ul class="header-user-popover-list">
                        <li class="header-user-popover-list-item">
                            <a href="/contact" class="link--no-style">
                                <span class="popover-icon">
                                    <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-mail"></use>
                                    </svg>
                                </span>
                                <span>Kontakt</span>
                            </a>
                        </li>

                        <li class="header-user-popover-list-item">
                            <a href="/faq" class="link--no-style">
                                <span class="popover-icon">
                                    <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-book"></use>
                                    </svg>
                                </span>
                                <span>FAQ</span>
                            </a>
                        </li>

                        <li class="header-user-popover-list-item">
                            <a href="/help" class="link--no-style">
                                <span class="popover-icon">
                                    <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                        <use href="/assets/icons/vdb-icons.svg#icon-help"></use>
                                    </svg>
                                </span>
                                <span>Hilfe</span>
                            </a>
                        </li>
                    </ul>

                    <hr class="popover-sep">

                    <form method="post" action="/logout" class="header-user-popover-logout-form">
                        <input type="hidden" name="_csrf" value="<?= $headerEscape($csrfToken) ?>">

                        <button class="btn btn--primary-transp btn--md header-user-popover-logout" type="submit">
                            <span class="popover-icon">
                                <svg class="vdb-icon vdb-icon--lg vdb-icon--bold" aria-hidden="true">
                                    <use href="/assets/icons/vdb-icons.svg#icon-logout"></use>
                                </svg>
                            </span>
                            <span>Abmelden</span>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="header-bottom-user-login">
                    <a href="/login" class="btn btn--primary btn--md">
                        Anmelden
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
(function(){
    var nav = document.querySelector('.header-bottom-nav');

    function closeSubmenus(root) {
        var scope = root || document;
        scope.querySelectorAll('.submenu-open').forEach(function(item){
            item.classList.remove('submenu-open');
            var link = item.querySelector(':scope > a[aria-expanded]');
            if (link) {
                link.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function closeSiblingSubmenus(item) {
        if (!item || !item.parentElement) {
            return;
        }

        Array.prototype.forEach.call(item.parentElement.children, function(sibling){
            if (sibling !== item && sibling.classList && sibling.classList.contains('submenu-open')) {
                closeSubmenus(sibling);
                sibling.classList.remove('submenu-open');
                var siblingLink = sibling.querySelector(':scope > a[aria-expanded]');
                if (siblingLink) {
                    siblingLink.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    if (nav) {
        nav.addEventListener('click', function(e){
            var link = e.target.closest('.has-submenu > a');
            if (!link || !nav.contains(link)) {
                return;
            }

            var item = link.parentElement;
            if (!item.classList.contains('submenu-open')) {
                e.preventDefault();
                closeSiblingSubmenus(item);
                item.classList.add('submenu-open');
                link.setAttribute('aria-expanded', 'true');
            }
        });
    }

    document.addEventListener('click', function(e){
        if (!nav || nav.contains(e.target)) {
            return;
        }

        closeSubmenus(document);
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            closeSubmenus(document);
        }
    });
})();

(function(){
    function closeAll() {
        document.querySelectorAll('.header-user-popover[open]').forEach(function(el){
            el.removeAttribute('open');
        });
    }

    document.addEventListener('click', function(e){
        var btn = e.target.closest('[popovertarget]');
        if (btn) {
            var id = btn.getAttribute('popovertarget');
            var pop = document.getElementById(id);

            if (pop) {
                var isOpen = pop.hasAttribute('open');

                if (isOpen) {
                    pop.removeAttribute('open');
                } else {
                    closeAll();
                    pop.setAttribute('open', '');
                }
            }

            return;
        }

        if (!e.target.closest('.header-user-popover')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            closeAll();
        }
    });
})();
</script>