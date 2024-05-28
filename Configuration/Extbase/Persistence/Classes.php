<?php

use NITSAN\NsSocialLogin\Domain\Model\User;

return [
    User::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'TxNsSocialLoginSource' => [
                'fieldName' => 'tx_ns_social_login_source',
            ],
            'TxNsSocialLoginIdentifier' => [
                'fieldName' => 'tx_ns_social_login_identifier',
            ],
        ],
    ],
];
