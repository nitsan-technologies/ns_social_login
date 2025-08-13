<?php

if (!function_exists('getCheckboxConfig')) {

    /**
     * getCheckboxConfig
     *
     * @param string $labelKey
     * @return array
     */
    function getCheckboxConfig(string $labelKey): array
    {
        return [
            'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.'.$labelKey,
            'config' => [
                'type' => 'check',
            ],
            'onChange' => 'reload',
        ];
    }
}

if (!function_exists('getInputConfig')) {

    /**
     * getInputConfig
     *
     * @param string $labelKey
     * @param string $displayCond
     * @return array
     */
    function getInputConfig(string $labelKey, string $displayCond): array
    {
        return [
            'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.'.$labelKey,
            'displayCond' => 'FIELD:'.$displayCond.':=:1',
            'config' => [
                'type' => 'input',
            ],
        ];
    }
}

if (!function_exists('getDisplayModeConfig')) {

    /**
     * getDisplayModeConfig
     *
     * @param string $displayCond
     * @return array
     */
    function getDisplayModeConfig(string $displayCond): array
    {
        return [
            'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.displayMode',
            'displayCond' => 'FIELD:'.$displayCond.':=:1',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.displaymode.page', 'page'],
                    ['LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.displaymode.popup', 'popup'],
                ],
            ],
        ];
    }
}

if (!function_exists('getScopeConfig')) {

    /**
     * getScopeConfig
     *
     * @param string $displayCond
     * @return array
     */
    function getScopeConfig(string $displayCond): array
    {
        return [
            'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.scope',
            'displayCond' => 'FIELD:'.$displayCond.':=:1',
            'config' => [
                'type' => 'input',
                'default' => 'email',
            ],
        ];
    }
}

$siteColumns = &$GLOBALS['SiteConfiguration']['site']['columns'];

$siteColumns['facebook_enable'] = getCheckboxConfig('enable_facebook');
$siteColumns['facebook_appid'] = getInputConfig('appid', 'facebook_enable');
$siteColumns['facebook_app_secret'] = getInputConfig('appSecret', 'facebook_enable');
$siteColumns['facebook_app_scope'] = getScopeConfig('facebook_enable');
$siteColumns['facebook_display_mode'] = getDisplayModeConfig('facebook_enable');


// Storage
$siteColumns['storage_page'] = [
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.storagePid',
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
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.defaultUserGroup',
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
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.fileStorage',
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
    'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:nssociallogin.avtarPath',
    'config' => [
        'type' => 'input',
        'default' => 'user_upload',
    ],
];

$GLOBALS['SiteConfiguration']['site']['palettes'] = array_merge_recursive(
    $GLOBALS['SiteConfiguration']['site']['palettes'],
    [
        'facebook' => [
            'showitem' => 'facebook_enable, 
            --linebreak--, facebook_appid, facebook_app_secret, 
            --linebreak--, facebook_app_scope, facebook_display_mode',
        ],
      
        'storage' => [
            'showitem' => 'storage_page, usergroup, 
            --linebreak--, file_storage,avatar_image',
        ],
]
);

//Defined show item...
$GLOBALS['SiteConfiguration']['site']['types'][0]['showitem'] .= ',
    --div--;Providers, 
    --palette--;Facebook;facebook,
    --palette--;Storage;storage
';