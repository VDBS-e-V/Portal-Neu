<?php

use App\Presentation\Templating\Renderer;

return [
    Renderer::class => function (array $c) {
        return new Renderer(basePath: $c['paths.views']);
    },
];