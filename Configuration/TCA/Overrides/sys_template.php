<?php

defined('TYPO3') or defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'ns_social_login',
    'Configuration/TypoScript',
    'Social Login'
);
