<?php

defined('TYPO3') or defined('TYPO3_MODE') || die();
    
$typo3VersionArray = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionStringToArray(
    \TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version()
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    'ns_social_login',
    'auth' /* sv type */,
    \NITSAN\NsSocialLogin\Service\SocialLoginService::class /* sv key */,
    [
        'title' => 'Social Authentication Service',
        'description' => 'Authentication for users from social providers (facebook, twitter...)',
        'subtype' => 'authUserFE,getUserFE',
        'available' => true,
        'priority' => 82, /* will be called before default typo3 authentication service */
        'quality' => 82,
        'os' => '',
        'exec' => '',
        'className' => \NITSAN\NsSocialLogin\Service\SocialLoginService::class,
    ]
);


$GLOBALS['TYPO3_CONF_VARS']['SVCONF']['auth']['setup']['FE_fetchUserIfNoSession'] = true;
// @extensionScannerIgnoreLine
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['logoff_post_processing']['ns_social_login'] = 'NITSAN\NsSocialLogin\Hooks\LogOffHook->postProcessing';

if (version_compare($typo3VersionArray['version_main'], '11', '>=')) {
    $authController = \NITSAN\NsSocialLogin\Controller\AuthController::class;
    $name = 'NsSocialLogin';
} else {
    $authController = 'Auth';
    $name =  'NITSAN.NsSocialLogin';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions']['clearCloudflareCache'] = 
    \NITSAN\NsSocialLogin\Hooks\ClearCacheHook::class;

}
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    $name,
    'Pi1',
    [
        $authController => 'list, connect',
    ],
    // non-cacheable actions
    [
        $authController => 'list, connect',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    $name,
    'Pi2',
    [
        $authController => 'endpoint, list',
    ],
    // non-cacheable actions
    [
        $authController => 'endpoint, list',
    ]
);
    

//add marker to felogin if is loaded
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('felogin')) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['postProcContent']['ns_social_login'] = 'NITSAN\NsSocialLogin\Hooks\FeLoginHook->postProcContent';
}

#Exclude some params
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_nssociallogin_pi1[provider]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_nssociallogin_pi1[redirect]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_nssociallogin_pi1[error]';

//globals namespace for viewhelper
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['sociallogin'] = ['NITSAN\\NsSocialLogin\\ViewHelpers'];
