<?php

declare(strict_types=1);

use Zing\LaravelSubscribe\Subscription;

return [
    'models' => [
        'user' => \App\User::class,
        'subscription' => Subscription::class,
    ],
    'table_names' => [
        'subscriptions' => 'subscriptions',
    ],
    'column_names' => [
        'user_foreign_key' => 'user_id',
    ],
];
