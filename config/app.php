<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') !== false && getenv('APP_NAME') !== '' ? getenv('APP_NAME') : 'VDBS Portal',
    'env' => getenv('APP_ENV') !== false && getenv('APP_ENV') !== '' ? getenv('APP_ENV') : 'local',
    'debug' => filter_var(getenv('APP_DEBUG') !== false ? getenv('APP_DEBUG') : '0', FILTER_VALIDATE_BOOL),
    'base_url' => getenv('APP_BASE_URL') !== false ? rtrim((string) getenv('APP_BASE_URL'), '/') : '',
];