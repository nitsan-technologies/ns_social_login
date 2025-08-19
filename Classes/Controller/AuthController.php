<?php

namespace NITSAN\NsSocialLogin\Controller;

use Hybridauth\Storage\Session;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use NITSAN\NsSocialLogin\Utility\AuthUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use NITSAN\NsSocialLogin\Utility\SiteConfigUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * AuthController
 */
class AuthController extends ActionController
{
    /**
     * @var array
     */
    protected $extConfig = [];

    /**
     * @var SiteFinder
     */
    protected $site;

    /**
     * @var Session
     */
    protected $hybridStorageSession;

    protected function initializeAction(): void
    {
        $this->extConfig = SiteConfigUtility::getAllConstants();
        $this->hybridStorageSession = new Session();
        parent::initializeAction();
    }

    /**
     * List action
     */
    public function listAction()
    {
        $providers = $this->getProviderData();
        $styleConfig = $this->settings['styleConfig']['style']['style'] ?? 's1';
        if (isset($this->settings['useGlobalStyle']) && (int)$this->settings['useGlobalStyle'] == 0) {
            $styleConfig = $this->settings['style'] ?? 's1';
        }
        $isRedirect = false;
        if ($this->getCurrentVersion() >= 11) {
            $params = $this->request->getQueryParams();
            $isRedirect = (
                (isset($params['tx_nssociallogin_pi2']['isRedirect']) && $params['tx_nssociallogin_pi2']['isRedirect'] === '1') ||
                (isset($params['tx_nssociallogin_pi1']['isRedirect']) && $params['tx_nssociallogin_pi1']['isRedirect'] === '1')
            );
        } else {
            if (
                // @extensionScannerIgnoreLine
                (isset(GeneralUtility::_GET('tx_nssociallogin_pi1_pi2')['isRedirect']) && GeneralUtility::_GET('tx_nssociallogin_pi1_pi2')['isRedirect'] === '1') ||
                (isset(GeneralUtility::_GET('tx_nssociallogin_pi1_pi1')['isRedirect']) && GeneralUtility::_GET('tx_nssociallogin_pi1_pi1')['isRedirect'] === '1')
            ) {
                $isRedirect = true;
            }
        }

        if ($isRedirect) {
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addJsInlineCode(
                'closePopup',
                '
                    window.close()
                '
            );
        }

        $this->view->assignMultiple([
            'providers' => $providers,
            'extConfig' => $this->extConfig,
            'styleConfig' => $styleConfig,
        ]);

        // If user is disabled from BE, then we clear the session and add note in frontend.
        $frontendUser = $this->request->getAttribute('frontend.user');
        if ($frontendUser instanceof FrontendUserAuthentication) {
            $userSession = $frontendUser->getSession();
            if ($userSession->getUserId() != 0 && empty($userSession->getData())) {
                $context = GeneralUtility::makeInstance(Context::class);
                if ($context->getPropertyFromAspect('frontend.user', 'isLoggedIn')) {
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
                    if (!$userSession->hasData()) {
                        $frontendUser->removeSessionData();
                        $frontendUser->removeCookie('PHPSESSID');
                    }
                }
                $this->view->assign('userDisabled', LocalizationUtility::translate('userDisabled', 'NsSocialLogin'));
                return $this->htmlResponse();
            }
        }

        if ($this->getCurrentVersion() >= 11) {
            return $this->htmlResponse();
        }
    }

    /**
     * Connect action
     */
    public function connectAction(): void
    {
        $provider = $this->hybridStorageSession->get('provider');
        if ($provider == '') {
            throw new \Exception('Provider is required', 1325691094);
        }
        $context = GeneralUtility::makeInstance(Context::class);
        $redirectionUri = null;
        //redirect if login
        $frontendUser = $this->request->getAttribute('frontend.user');
        if (
            $context->getPropertyFromAspect('frontend.user', 'isLoggedIn') &&
            $frontendUser instanceof FrontendUserAuthentication && is_array($frontendUser->user)
        ) {
            $redirectionUri = $this->request->getArgument('redirect');
            //sanitize url with logintype=logout
            $redirectionUri = preg_replace('/(&?logintype=logout)/i', '', $redirectionUri);
        }
        if ($redirectionUri === null) {
            // Get current page ID from request attributes instead of TSFE
            $pageArguments = $this->request->getAttribute('routing');
            $pageId = $pageArguments ? $pageArguments->getPageId() : 0;
            $this->uriBuilder->setTargetPageUid((int)$pageId);
            $redirectionUri = $this->uriBuilder->build();
        }
        $this->hybridStorageSession->set('provider', '');
        $this->redirectToUri($redirectionUri);
    }

    /**
     * Endpoint action
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function endpointAction()
    {
        $currentProvider = $this->hybridStorageSession->get('endPointProvider');
        $isRedirect = false;
        if ($currentProvider != '' && $this->extConfig[$currentProvider . '_display_mode'] == 'popup') {
            $isRedirect = true;
        }
        $this->hybridStorageSession->set('provider', '');
        $this->hybridStorageSession->set('endPointProvider', '');
        if ($this->getCurrentVersion() >= 11) {

            return $this->redirect('list', 'Auth', 'NsSocialLogin.Pi1', ['isRedirect' => $isRedirect]);
        }
        return $this->redirect('list', 'Auth', 'NsSocialLogin.Pi1', ['isRedirect' => $isRedirect]);
    }

    /**
     * @return array
     */
    private function getProviderData(): array
    {
        $providers = [
            'facebook'
        ];
        foreach ($providers as $key => $provider) {
            if (isset($this->extConfig[$provider . '_enable'])) {
                if (!$this->extConfig[$provider . '_enable']) {
                    unset($providers[$key]);
                } else {
                    $providers[] = [
                        'name' => $provider,
                        'displayMode' => isset($this->extConfig[$provider . '_display_mode']) ? $this->extConfig[$provider . '_display_mode'] : '',
                    ];
                    unset($providers[$key]);
                }
            } else {
                unset($providers[$key]);
            }
        }

        return $providers;
    }

    /**
     * @return int
     */
    private function getCurrentVersion(): int
    {
        $typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(
            VersionNumberUtility::getCurrentTypo3Version()
        );

        return (int)$typo3VersionArray['version_main'];
    }

    public function clearCache()
    {
        $hybridStorageSession = new Session();
        $hybridStorageSession->set('provider', '');

        return new JsonResponse([
            'success' => true,
            'title' => LocalizationUtility::translate('clear_cache_success', 'NsSocialLogin'),
        ]);
    }
}
