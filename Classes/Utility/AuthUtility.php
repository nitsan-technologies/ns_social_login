<?php

namespace NITSAN\NsSocialLogin\Utility;

use Hybridauth\Hybridauth;
use Hybridauth\Storage\Session;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

/**
 * Class AuthUtility
 */
class AuthUtility
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $extConfig = [];

    /**
     * @var \Hybridauth\Hybridauth
     */
    protected $hybridAuth;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;


    protected $site;

    public function __construct()
    {
        $this->site = GeneralUtility::makeInstance(SiteFinder::class);
        $this->extConfig = SiteConfigUtility::getAllConstants();
        $this->config['callback'] = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . 'oauth';
        if (isset($this->extConfig['facebook_enable']) && $this->extConfig['facebook_enable']) {
            $this->config['providers']['Facebook'] = [
                'enabled' =>  $this->extConfig['facebook_enable'],
                'keys'    => [
                    'id' => $this->extConfig['facebook_appid'],
                    'secret' => $this->extConfig['facebook_app_secret'],
                ],
                'scope'   => $this->extConfig['facebook_app_scope'],
                'display' => $this->extConfig['facebook_display_mode'],
            ];
        }
       
    
    
       
        /* @var $logManager LogManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $this->logger = $logManager->getLogger(__CLASS__);
        $this->hybridAuth = new Hybridauth($this->config);
    }

    /**
     * @param string $provider
     */
    public function authenticate(string $provider)
    {
        $socialUser = null;
        try {
            $hybridAuth = new Hybridauth($this->config);
            $service = $hybridAuth->authenticate($provider);
            $socialUser = $service->getUserProfile();
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
            $logManager = GeneralUtility::makeInstance(LogManager::class);
            $this->logger = $logManager->getLogger(__CLASS__);
            $this->logger->log(
                LogLevel::ERROR,
                $error
            );
            $hybridStorageSession = new Session();
            $hybridStorageSession->set('provider', '');
            echo $exception->getMessage();
            die;
        }
        if ($socialUser !== null) {
            return $socialUser;
        }
        return false;
    }

    /**
     * @param string $provider
     *
     * @return bool
     */
    public function isConnectedWithProvider(string $provider): bool
    {
        return $this->hybridAuth->isConnectedWith($provider);
    }

    /**
     * logout from all providers when typo3 logout takes place
     */
    public function logout(): void
    {
        $adapters = $this->hybridAuth->getConnectedAdapters();
        if (isset($adapters[0])) {
            $adapters[0]->disconnect();
        }
    }
}
