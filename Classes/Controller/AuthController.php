<?php

namespace NITSAN\NsSocialLogin\Controller;

use Hybridauth\Storage\Session;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use NITSAN\NsSocialLogin\Utility\SiteConfigUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * AuthController
 */
class AuthController extends ActionController
{
    /**
     * extConfig
     *
     * @var array
     */
    protected array $extConfig = [];

    /**
     * site
     *
     * @var SiteFinder
     */
    protected SiteFinder $site;

    /**
     * @var Session
     */
    protected Session $hybridStorageSession;

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
        $provider = $this->getProviderData();

        $this->view->assignMultiple([
            'provider' => $provider,
            'extConfig' => $this->extConfig,
        ]);

        if ($this->getCurrentVersion() >= 11) {
            return $this->htmlResponse();
        }
    }

    /**
     * Connect action
     */
    public function connectAction()
    {
        $provider = $this->hybridStorageSession->get('provider');
        if ($provider == '') {
            throw new \Exception('Provider is required', 1325691094);
        }
        $context = GeneralUtility::makeInstance(Context::class);
        $redirectionUri = null;
        //redirect if login
        if ($context->getPropertyFromAspect('frontend.user', 'isLoggedIn') && is_array($GLOBALS['TSFE']->fe_user->user)) {
            $redirectionUri = $this->request->getArgument('redirect');
            //sanitize url with logintype=logout
            $redirectionUri = preg_replace('/(&?logintype=logout)/i', '', $redirectionUri);
        }
        if ($redirectionUri === null) {
            $this->uriBuilder->setTargetPageUid((int)$GLOBALS['TSFE']->id);
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
        $this->hybridStorageSession->set('provider', '');
        $this->hybridStorageSession->set('endPointProvider', '');
        if ($this->getCurrentVersion() >= 11) {
            return $this->redirect('list', 'Auth', 'NsSocialLogin');
        }
        return $this->redirect('list', 'Auth', 'NsSocialLogin.Pi1');
    }

    /**
     * @return array
     */
    private function getProviderData(): array
    {

        if (!$this->extConfig['facebook_enable']) {
            return [];
        } else {
            return [
                'name' => 'facebook',
                'displayMode' => 'page',
            ];
        }
    }

    /**
     * @return int
     */
    private function getCurrentVersion()
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
