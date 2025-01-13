<?php

return [
    'app' => [
        'metadata' => 'annotations',
        'classes' => [
            PageContent::class,
        ],
        'directories' => [
            [
                'path' => __DIR__ . '/../classes/component/app',
                'namespace' => 'App',
            ],
            [
                'path' => __DIR__ . '/../classes/component/ext',
                'namespace' => 'Ext',
            ],
        ],
        'views' => [
            'component' => [
                'directory' => dirname(__DIR__) . '/templates',
                'extension' => '.php',
                'renderer' => 'jaxon',
            ],
        ],
        'options' => [
            'views' => [
                'default' => 'component',
            ],
        ],
        'dialogs' => [
            'default' => [
                'modal' => 'bootbox',
                'alert' => 'noty',
                'confirm' => 'noty',
            ],
            'toastr' => [
                'options' => [
                    'alert' => [
                        'closeButton' => true,
                        'positionClass' => 'toast-top-center',
                    ],
                ],
            ],
            'lib' => [
                'use' => ['bootstrap'],
            ],
        ],
    ],
    'lib' => [
        'core' => [
            'debug' => [
                'on' => false,
            ],
            'request' => [
                'uri' => 'ajax.php',
            ],
            'prefix' => [
                'class' => '',
            ],
        ],
        'js' => [
            'lib' => [
                'uri' => '/js',
            ],
        ],
    ],
];
