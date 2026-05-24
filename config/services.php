<?php

use App\Presentation\Templating\Renderer;
use App\Infrastructure\Persistence\PDO\MenuPdoRepository;
use PDO;

return [
    Renderer::class => function (array $c) {
        return new Renderer(basePath: $c['paths.views']);
    },

    // PDO-backed Menu repository
    MenuPdoRepository::class => function (array $c) {
        $dbConfig = $c['config.db'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $dbConfig['host'], (int)$dbConfig['port'], $dbConfig['name'], $dbConfig['charset']);
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        return new MenuPdoRepository($pdo);
    },
];