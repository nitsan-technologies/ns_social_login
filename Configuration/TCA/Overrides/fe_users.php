<?php

defined('TYPO3') or defined('TYPO3_MODE') || die();

/**
 * Add extra fields to the fe_users
 */
$tca = [
    'tx_ns_social_login_source' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_db.xlf:fe_users.tx_ns_social_login_source',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['None', 0],
                ['Facebook', 1]
            ],
            'size' => 1,
            'maxitems' => 1,
        ],
    ],
    'tx_ns_social_login_identifier' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang_db.xlf:fe_users.tx_ns_social_login_identifier',
        'config' => [
            'type' => 'input',
            'size' => '10',
            'readOnly' => 1,
            'default'=> 0,
        ],
    ],
];
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tca);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:ns_social_login/Resources/Private/Language/locallang_db.xlf:fe_users.tab.social, tx_ns_social_login_source, tx_ns_social_login_identifier');
