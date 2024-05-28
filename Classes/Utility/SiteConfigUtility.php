<?php

namespace NITSAN\NsSocialLogin\Utility;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteConfigUtility
{
    public static function getAllConstants(): array
    {
        $uri = GeneralUtility::makeInstance(Uri::class, GeneralUtility::getIndpEnv('TYPO3_REQUEST_DIR'));
        $request = GeneralUtility::makeInstance(ServerRequest::class, $uri);
        $matcher = GeneralUtility::makeInstance(SiteMatcher::class);
        $routeResult = $matcher->matchRequest($request);
        return $routeResult->getSite()->getConfiguration();
    }
}
