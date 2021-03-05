<?php

return [
    'category' => [
        'name'    => 'Categories',
        'actions' => [
            'create' => TRUE,
            'read'   => TRUE,
            'update' => TRUE,
            'delete' => TRUE,
        ],
    ],

    'user' => [
        'name'    => 'User',
        'actions' => [
            'create' => TRUE,
            'read'   => TRUE,
            'update' => TRUE,
            'delete' => TRUE,
        ],
    ],

    'news' => [
        'name'    => 'News',
        'actions' => [
            'deleted' => TRUE,
            'all'     => TRUE,
            'publish' => TRUE,
            'logs'    => TRUE,
            'create'  => TRUE,
            'read'    => TRUE,
            'update'  => TRUE,
            'delete'  => TRUE,
        ],
    ],

    'settings' => [
        'name'    => 'Settings',
        'actions' => [
            'create'  => FALSE,
            'read'    => FALSE,
            'update'  => TRUE,
            'delete'  => FALSE,
        ],
    ],

    'notification' => [
        'name' => 'Notification',
        'actions' => [
            'create'  => FALSE,
            'read'    => FALSE,
            'update'  => FALSE,
            'delete'  => TRUE,
        ]
    ],
];
