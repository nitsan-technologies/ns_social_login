<?php

$EM_CONF['ns_social_login'] = [
    'title' => 'Social Login',
    'description' => 'https://t3planet.com/ns-social-login-typo3-extension',
    'category' => 'plugin',
    'author' => 'T3:Rohan Parmar, T3:Nilesh Malankiya, QA:Shrijay Mori',
    'author_company' => 'T3Planet // NITSAN',
    'author_email' => 'sanjay@nitsan.in',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-12.5.99',
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
