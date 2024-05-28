<?php

namespace NITSAN\NsSocialLogin\Hooks;

use Hybridauth\Storage\Session;
use NITSAN\NsSocialLogin\Utility\AuthUtility;
use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LogOffHook
{
    /**
     * @param array $params
     * @param AbstractUserAuthentication $pObj
     */
    public function postProcessing(array $params, AbstractUserAuthentication $pObj): void
    {
        if ($pObj->loginType !== 'FE') {
            return;
        }
        try {
            /** @var AuthUtility $authUtility */
            $authUtility = GeneralUtility::makeInstance(AuthUtility::class);
            // @extensionScannerIgnoreLine
            $authUtility->logout();
            $hybridStorageSession = new Session();
            $hybridStorageSession->set('provider', '');
        } catch (\Exception $e) {
        }
        //remove session user
        $pObj->removeSessionData();
        $pObj->removeCookie('PHPSESSID');
    }
}
