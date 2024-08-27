<?php

$EM_CONF['ns_social_login'] = [
    'title' => 'Social Login and Register',
    'description' => 'Meet our all-in-one TYPO3 Social Login and Register extension, which allows users to log in and register using various social media accounts directly from your TYPO3 frontend. With this extension, users can easily access your site using popular platforms like Facebook, Google, Twitter, LinkedIn, Instagram, OpenID, Apple, Discord, Keycloak, Slack, Telegram, Dropbox, and more. Get seamless, one-click frontend login with the TYPO3 Social Login extension. Explore the Demo, Product Page, Documentation & Support: https://t3planet.com/typo3-social-login-extension',
    'category' => 'plugin',
    'author' => 'T3:Rohan Parmar, T3:Nilesh Malankiya, QA:Krishna Dhapa',
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
