<?php

namespace ItemSetCalendar;

return [
    'controller_plugins' => [
        'factories' => [
            'itemSetCalendar' => Service\ControllerPlugin\ItemSetCalendarFactory::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'ItemSetCalendar\Controller\Site\Item' => Controller\Site\ItemController::class,
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'ItemSetCalendar\Form\ConfigForm' => Form\ConfigForm::class,
        ],
    ],
    'router' => [
        'routes' => [
            'site' => [
                'child_routes' => [
                    'item-set' => [
                        'options' => [
                            'defaults' => [
                                '__NAMESPACE__' => 'ItemSetCalendar\Controller\Site',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
