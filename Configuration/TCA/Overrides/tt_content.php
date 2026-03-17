<?php

defined('TYPO3') or defined('TYPO3_MODE') || die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$typo3VersionArray = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionStringToArray(
    \TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version()
);

if (version_compare((string)$typo3VersionArray['version_main'], '12', '<')) {
    // TYPO3 11 and below — keep original behavior
    $name = version_compare((string)$typo3VersionArray['version_main'], '11', '>=')
        ? 'NsSocialLogin'
        : 'NITSAN.NsSocialLogin';

    ExtensionUtility::registerPlugin(
        $name,
        'Pi1',
        'Social Login'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['nssociallogin_pi1'] = 'pi_flexform';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['nssociallogin_pi1'] = 'recursive,select_key,pages';

    ExtensionManagementUtility::addPiFlexFormValue(
        'nssociallogin_pi1',
        'FILE:EXT:ns_social_login/Configuration/FlexForms/flexform_list.xml'
    );

} else {
   $pluginSignature = ExtensionUtility::registerPlugin(
    'NsSocialLogin',
    'Pi1',
    'Social Login',
    'ns_social_login-plugin-pi1',
    'plugins'
);

// @extensionScannerIgnoreLine
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:ns_social_login/Configuration/FlexForms/flexform_list.xml',
    $pluginSignature
);

// Add plugin tab with flexform scoped to this plugin only
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,pi_flexform',
    $pluginSignature,
    'after:subheader'
);
}