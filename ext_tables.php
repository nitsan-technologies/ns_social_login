<?php

defined('TYPO3') or defined('TYPO3_MODE') || die();

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

$icon = [
    'plugin-nssociallogin',
    'module-nshelpdesk',
    'parent-module-nshelpdesk'
];

$iconRegistry->registerIcon(
    'plugin-nssociallogin',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:ns_social_login/Resources/Public/Icons/plugin-nssociallogin.svg']
);

$iconRegistry->registerIcon(
    'nitsan',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:ns_social_login/ext_icon.svg']
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    "@import 'EXT:ns_social_login/Configuration/TSconfig/ContentElementWizard.tsconfig'"
);
