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
    'view_manager' => [
        'template_map' => [
            'navigation' => __DIR__ . '/../view/partials/navigation.phtml',
            'flashmessenger' => __DIR__ . '/../view/partials/flashmessenger.phtml',
            'config' => __DIR__ . '/../view/config/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];