<?php
declare(strict_types=1);

function vdb_icon(
    string $name,
    string $color = 'current',
    string $size = 'md',
    string $label = '',
    string $extraClass = ''
): string {
    $allowedColors = [
        'current', 'amber', 'teal', 'berry', 'sun-yellow', 'lime', 'plum',
        'gray', 'dark', 'white', 'black'
    ];

    $allowedSizes = ['2xs', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl'];

    if (!in_array($color, $allowedColors, true)) {
        $color = 'current';
    }

    if (!in_array($size, $allowedSizes, true)) {
        $size = 'md';
    }

    $safeName = preg_replace('/[^a-z0-9\-]/', '', strtolower($name)) ?: 'help';
    $safeExtraClass = trim(preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $extraClass));
    $classes = trim("vdb-icon vdb-icon--{$color} vdb-icon--{$size} {$safeExtraClass}");

    $aria = $label !== ''
        ? 'role="img" aria-label="' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '"'
        : 'aria-hidden="true" focusable="false"';

    return '<svg class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '" ' . $aria . '>' .
        '<use href="/assets/icons/vdb-icons.svg#icon-' . htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8') . '"></use>' .
        '</svg>';
}

function vdb_icon_link(
    string $href,
    string $icon,
    string $text,
    string $color = 'teal',
    string $size = 'sm'
): string {
    return '<a class="vdb-icon-link" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' .
        vdb_icon($icon, $color, $size) .
        '<span>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</span>' .
        '</a>';
}
