<?php

namespace NITSAN\NsSocialLogin\Hooks;

use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;


class BackendUserLogin
{
    protected $objectManager;
    protected $nsLicenseModule;
    public function dispatch($backendUser)
    {
        $typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(
            VersionNumberUtility::getCurrentTypo3Version()
        );
        // Let's check license system
        $isLicenseActivate = GeneralUtility::makeInstance(PackageManager::class)->isPackageActive('ns_license');
        if ($isLicenseActivate) {
            if (version_compare($typo3VersionArray['version_main'], 12, '>=')) {
                $this->nsLicenseModule = GeneralUtility::makeInstance(\NITSAN\NsLicense\Service\LicenseService::class);
            } else {
                // @extensionScannerIgnoreLine
                $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                $this->nsLicenseModule = $this->objectManager->get(\NITSAN\NsLicense\Controller\NsLicenseModuleController::class);
            }
            $this->nsLicenseModule->connectToServer('ns_social_login', 0);
        }
    }
}
