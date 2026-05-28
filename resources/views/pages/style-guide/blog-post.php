<?php declare(strict_types=1); ?>

<?php
$intro = 'Vorlage für längere Inhalte mit Hero, Meta-Angaben und Inhaltsblöcken.';
$examples = [[
    'title' => 'Artikelstruktur',
    'lead' => 'Ein kompakter Blogbeitrag mit Titel, Metadaten und Teaser.',
    'html' => <<<'HTML'
<article class="blog-post">
    <header class="blog-post__header">
        <p class="blog-post__meta">12. Mai 2026 · Redaktion</p>
        <h2 class="blog-post__title">Ein sauberer Einstieg in den Artikel</h2>
    </header>
    <p class="blog-post__lead">Der Blog-Post zeigt, wie Inhalte mit klarer Hierarchie und lesbarer Typografie aufgebaut werden.</p>
    <a class="btn btn--outline btn--sm" href="#">Weiterlesen</a>
</article>
HTML,
]];
require __DIR__ . '/_component-page.php';
