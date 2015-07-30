<?php

return [

    //Describe the routes of service
    'router' => [
        'routes' => [
            'api' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api[/:controller[/[:id]]]',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => 'Customers',
                    ],
                ],
            ],
        ],
    ],

    'doctrine' => array(
        'driver' => array(
            'application_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Application/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'application_entities'
                )
            )
        )
    ),

    //Connect services
    'service_manager' => [
        'factories' => array(),
    ],

    //List controllers
    'controllers' => [
        'invokables' => [
            'Customers' => 'Application\Controller\CustomersController',
            'Calls' => 'Application\Controller\CallsController',
        ],
    ],

    //View manager settings
    'view_manager' => [

        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',

        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],

        'template_path_stack' => [
            __DIR__ . '/../view',
        ],

        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
            ]
        ]
    ],
];