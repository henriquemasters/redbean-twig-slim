<?php

return [
    'settings' => [
        // comment this line when deploy to production environment
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
        // View settings
        'view' => [
            'template_path' => __DIR__ . '/views',
            'twig' => [
                'cache' => false,   //  'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,   //  'debug' => false,
                'auto_reload' => true,
            ],
        ],
        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../log/app.log',
        ],
    ],
];
