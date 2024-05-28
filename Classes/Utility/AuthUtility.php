<?php

namespace NITSAN\NsSocialLogin\Utility;

use Hybridauth\Hybridauth;
use Hybridauth\Storage\Session;
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
    protected array $config = [];

    /**
     * @var array
     */
    protected array $extConfig = [];

    /**
     * @var Hybridauth $hybridAuth
     */
    protected Hybridauth $hybridAuth;

    /**
     * @var ConfigurationManager
     */
    protected ConfigurationManager $configurationManager;

    protected $site;

    public function __construct()
    {
        $this->site = GeneralUtility::makeInstance(SiteFinder::class);
        $this->extConfig = SiteConfigUtility::getAllConstants();
        $this->config['callback'] = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . '?type=1712813073';
        if (isset($this->extConfig['facebook_enable']) && $this->extConfig['facebook_enable']) {
            $this->config['providers']['Facebook'] = [
                'enabled' =>  $this->extConfig['facebook_enable'],
                'keys'    => [
                    'id' => $this->extConfig['facebook_appid'],
                    'secret' => $this->extConfig['facebook_app_secret'],
                ],
                'scope'   => 'email',
                'display' => 'page',
            ];
        }
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
