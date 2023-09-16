<?php

return [
    'role_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
			'games' => 'c,r,u,d',
			'cards' => 'c,r,u,d',
            'profile' => 'r,d'
        ],
    ],
    'user_roles' => [
        'admin' => [
            ['name' => "Admin", "email" => "admin@admin.com", "password" => 'admin'],
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
