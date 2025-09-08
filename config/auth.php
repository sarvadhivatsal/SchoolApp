<?php

return [

    'defaults' => [
        'guard' => 'web',   // can also be 'sanctum' if API
        'passwords' => 'accounts',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'accounts',
        ],
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'accounts',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
        'teacher' => [
            'driver' => 'session',
            'provider' => 'teachers',
        ],
    ],

    'providers' => [
        'accounts' => [
            'driver' => 'eloquent',
            'model' => App\Models\Account::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Account::class,
        ],
        'teachers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Account::class,
        ],
    ],

    'passwords' => [
        'accounts' => [
            'provider' => 'accounts',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
