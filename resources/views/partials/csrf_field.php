<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$csrfToken = (string) ($csrfToken ?? '');
?>

<?php if ($csrfToken !== ''): ?>
    <input type="hidden" name="_csrf_token" value="<?= $e($csrfToken) ?>">
<?php endif; ?>
