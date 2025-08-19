<?php

use NITSAN\NsSocialLogin\Controller\AuthController;

return [
    'clear_cache' => [
        'path' => '/sociallogin/clearcache',
        'target' => AuthController::class . '::clearCache',
    ],
];
