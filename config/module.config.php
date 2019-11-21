<?php 

return [
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
            ],
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'functions' => Functions::class,
        ],
        'factories' => [
            Functions::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'navigation' => __DIR__ . '/../view/partials/navigation.phtml',
            'flashmessenger' => __DIR__ . '/../view/partials/flashmessenger.phtml',
            'config' => __DIR__ . '/../view/config/index.phtml',
            'midnet/subform' => __DIR__ . '/../view/partials/subform.phtml',
            'midnet/subtable' => __DIR__ . '/../view/partials/subtable.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];