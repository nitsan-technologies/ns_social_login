<?php

namespace NITSAN\NsSocialLogin;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Setup
 */
class Setup
{
    protected $objectManager;
    protected $nsLicenseModule;
    public function executeOnSignal($extname = null)
    {
        $typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(
            VersionNumberUtility::getCurrentTypo3Version()
        );
        if (is_object($extname)) {
            $extname = $extname->getPackageKey();
        }

        if ($extname === 'ns_social_login') {
            // Let's check license system
            $activePackages = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Package\PackageManager::class)->getActivePackages();
            $isLicenseCheck = false;
            foreach ($activePackages as $key => $value) {
                if ($key == 'ns_license') {
                    $isLicenseCheck = true;
                }
            }
            if($isLicenseCheck) {
                if (version_compare($typo3VersionArray['version_main'], 12, '>=')) {
                    $this->nsLicenseModule = GeneralUtility::makeInstance(\NITSAN\NsLicense\Service\LicenseService::class);
                } else {
                    // @extensionScannerIgnoreLine
                    $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                    $this->nsLicenseModule = $this->objectManager->get(\NITSAN\NsLicense\Controller\NsLicenseModuleController::class);
                }

                $this->nsLicenseModule->connectToServer($extname, 0);
            }
        }
    }
}
