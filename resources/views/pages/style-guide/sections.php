<?php declare(strict_types=1); ?>

<?php
$intro = 'Abschnittsbausteine für saubere Seitenstruktur und Rhythmus.';
$examples = [[
    'title' => 'Content-Section',
    'lead' => 'Sektionen ordnen Inhalte und geben jedem Block Abstand.',
    'html' => <<<'HTML'
<section class="section">
    <h2 class="section__title">Abschnittstitel</h2>
    <p class="section__text">Hier steht begleitender Inhalt für die Section.</p>
</section>
HTML,
]];
require __DIR__ . '/_component-page.php';
