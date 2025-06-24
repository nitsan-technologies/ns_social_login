<?php

$EM_CONF['ns_social_login'] = [
    'title' => 'TYPO3 Social Login & Register',
    'description' => 'Enable users to log in and register via popular social platforms like Google, Facebook, LinkedIn, Apple, Discord, and more. A seamless one-click login experience directly on your TYPO3 frontend.',
    'category' => 'plugin',
    'author' => 'Team T3Planet',
    'author_company' => 'T3Planet',
    'author_email' => 'info@t3planet.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'classmap' => ['Classes/', 'Library/']
    ]
];
