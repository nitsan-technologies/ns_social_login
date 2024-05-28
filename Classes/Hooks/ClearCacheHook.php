<?php

namespace NITSAN\NsSocialLogin\Hooks;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// @extensionScannerIgnoreFile
class ClearCacheHook implements ClearCacheActionsHookInterface
{
    /**
     * Adds cache menu item.
     *
     * @param array $cacheActions
     * @param array $optionValues
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues): void
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $ajaxRoute = (string)$uriBuilder->buildUriFromRoute('ajax_clear_cache');
        $cacheActions[] = [
            'id' => 'cache_nssocial',
            'title' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:clear_cache',
            'description' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:clear_cache.description',
            'href' => $ajaxRoute,
            'iconIdentifier' => 'actions-system-cache-clear-impact-low',
        ];
        $optionValues[] = 'ns_social_login';
    }
}
