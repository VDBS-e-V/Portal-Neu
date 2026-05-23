<?php

return [
    'name' => getenv('APP_NAME') ?: 'App',
    'env' => getenv('APP_ENV') ?: 'prod',
    'debug' => (getenv('APP_DEBUG') ?: '0') === '1',
    'base_url' => getenv('APP_BASE_URL') ?: '',
];