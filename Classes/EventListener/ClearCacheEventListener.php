<?php

namespace NITSAN\NsSocialLogin\EventListener;

use TYPO3\CMS\Backend\Backend\Event\ModifyClearCacheActionsEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;

class ClearCacheEventListener
{
    /**
     * @var UriBuilder
     */
    private UriBuilder $uriBuilder;

    /**
     * @param UriBuilder $uriBuilder
     */
    public function __construct(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    public function __invoke(ModifyClearCacheActionsEvent $event): void
    {
        $event->addCacheAction([
            'id' => 'cache_nssocial',
            'title' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:clear_cache',
            'description' => 'LLL:EXT:ns_social_login/Resources/Private/Language/locallang.xlf:clear_cache.description',
            'href' => $this->uriBuilder->buildUriFromRoute('ajax_clear_cache'),
            'iconIdentifier' => 'actions-system-cache-clear-impact-low',
        ]);
        $event->addCacheActionIdentifier('ns_social_login');
    }
}
