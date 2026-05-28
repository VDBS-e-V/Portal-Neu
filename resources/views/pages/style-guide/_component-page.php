<?php declare(strict_types=1); ?>

<h1><?php echo htmlspecialchars($pageTitle ?? 'Style Guide', ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if (!empty($intro)) : ?>
<p><?php echo $intro; ?></p>
<?php endif; ?>

<?php foreach (($examples ?? []) as $example) : ?>
<section>
    <h2><?php echo htmlspecialchars($example['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php if (!empty($example['lead'])) : ?>
    <p><?php echo $example['lead']; ?></p>
    <?php endif; ?>
    <div>
        <?php echo $example['html']; ?>
    </div>
    <pre><code><?php echo htmlspecialchars(trim($example['html']), ENT_QUOTES, 'UTF-8'); ?></code></pre>
</section>
<?php endforeach; ?>
