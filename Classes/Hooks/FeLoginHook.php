<?php

namespace NITSAN\NsSocialLogin\Hooks;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FeLoginHook
{
    /**
     * @param array $params
     * @param $pObj
     * @return string
     */
    public function postProcContent(array $params, $pObj): string
    {
        $markerArray = [];
        $extConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('ns_social_login');
        $providers = [];
        foreach ($extConfig['providers'] as $key => $parameters) {
            if ($parameters['enabled'] == 1) {
                array_push($providers, rtrim($key, '.'));
            }
        }
        if (is_array($providers) && count($providers) > 0) {
            rsort($providers);
            //get redirect url if needed
            $pattern = '/<input(?:.*?)name=\"redirect_url\"(?:.*)value=\"([^"]+).*>/i';
            preg_match($pattern, $params['content'], $matches);
            $redirectUrl = isset($matches[1]) ? $matches[1] : GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
            foreach ($providers as $provider) {
                // @extensionScannerIgnoreLine
                $providerConf = $pObj->conf['socialauth_provider.'][$provider . '.'];
                $customTypolink = [
                    'parameter' => $GLOBALS['TSFE']->id,
                    'additionalParams' => '&type=1712813110&tx_sociallogin_pi1[provider]=' . $provider . '&tx_sociallogin_pi1[redirect]=' . $redirectUrl,
                ];
                $providerConf['typolink.'] = ($providerConf['typolink.']) ? array_merge($providerConf['typolink.'], $customTypolink) : $customTypolink;
                $markerArray['###NS_SOCIAL_LOGIN###'] = $pObj->cObj->stdWrap($markerArray['###NS_SOCIAL_LOGIN###'], $providerConf);
            }
            //wrap all
            $markerArray['###NS_SOCIAL_LOGIN###'] = $pObj->cObj->stdWrap($markerArray['###NS_SOCIAL_LOGIN###'], $pObj->conf['socialauth.']);
        }
        $templateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);
        // @extensionScannerIgnoreLine
        return $templateService->substituteMarkerArrayCached($params['content'], $markerArray);
    }
}
