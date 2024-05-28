<?php

$siteColumns = &$GLOBALS['SiteConfiguration']['site']['columns'];

$siteColumns['facebook_enable'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.enable_facebook',
    'config' => [
        'type' => 'check',
    ],
    'onChange' => 'reload',
];
$siteColumns['facebook_appid'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.appid',
    'displayCond' => 'FIELD:facebook_enable:=:1',
    'config' => [
        'type' => 'input',
    ],
];
$siteColumns['facebook_app_secret'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.appsecreate',
    'displayCond' => 'FIELD:facebook_enable:=:1',
    'config' => [
        'type' => 'input',
    ],
];

// Storage
$siteColumns['storage_page'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.storagePid',
    'config' => [
        'type' => 'input',
        'eval' => 'required,trim,int',
        'default' => 1,
        'range' => [
            'lower' => 0,
        ],
    ],
];
$siteColumns['usergroup'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.defaultUserGroup',
    'config' => [
        'type' => 'input',
        'eval' => 'required,trim,int',
        'default' => 1,
        'range' => [
            'lower' => 1,
        ],
    ],
];
$siteColumns['file_storage'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.fileStorage',
    'config' => [
        'type' => 'input',
        'eval' => 'required,trim,int',
        'default' => 1,
        'range' => [
            'lower' => 1,
        ],
    ],
];
$siteColumns['avatar_image'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_constant.xlf:nssociallogin.avtarPath',
    'config' => [
        'type' => 'input',
        'default' => 'user_upload',
    ],
];

$GLOBALS['SiteConfiguration']['site']['palettes'] = array_merge_recursive(
    $GLOBALS['SiteConfiguration']['site']['palettes'],
    [
        'facebook' => [
            'showitem' => 'facebook_enable, --linebreak--, facebook_appid, facebook_app_secret',
        ],
        'storage' => [
            'showitem' => '--div--;Providers,storage_page, usergroup, --linebreak--, file_storage,avatar_image',
        ],
]
);

//Defined show item...
$GLOBALS['SiteConfiguration']['site']['types'][0]['showitem'] .= ',--div--;Providers, --palette--;Facebook;facebook, --palette--;Storage;storage';
