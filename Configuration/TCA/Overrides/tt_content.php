<?php

defined('TYPO3') or defined('TYPO3_MODE') || die();

$typo3VersionArray = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionStringToArray(
    \TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version()
);

if (version_compare($typo3VersionArray['version_main'], '11', '>=')) {
    $name = 'NsSocialLogin';
} else {
    $name =  'NITSAN.NsSocialLogin';
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $name,
    'Pi1',
    'Social Login',
    'plugin-nssociallogin',
    'plugins'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['nssociallogin_pi1'] = 'recursive,select_key,pages';
