<?php
$e = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$fieldName = $csrfFieldName ?? '_csrf_token';
$token = $csrfToken ?? '';
?>

<input type="hidden" name="<?= $e($fieldName) ?>" value="<?= $e($token) ?>">
