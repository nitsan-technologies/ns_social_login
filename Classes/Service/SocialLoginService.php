<?php

namespace NITSAN\NsSocialLogin\Service;

use Hybridauth\Storage\Session;
use NITSAN\NsSocialLogin\Utility\AuthUtility;
use NITSAN\NsSocialLogin\Utility\SiteConfigUtility;
use TYPO3\CMS\Core\Authentication\AbstractAuthenticationService;
use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\Exception\IllegalFileExtensionException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFileWritePermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class SocialLoginService extends AbstractAuthenticationService
{
    /**
     * provider
     */
    protected string $provider;

    /**
     * @var array
     */
    protected $extConfig = [];

    /**
     * authUtility
     *
     * @var AuthUtility
     */
    protected AuthUtility $authUtility;

    protected $hybridStorageSession;

    protected int $currentTypo3Version = 0;

    /**
     * 100
     */
    const STATUS_AUTHENTICATION_FAILURE_CONTINUE = 100;

    /**
     * 200 - authenticated and no more checking needed - useful for IP checking without password
     */
    const STATUS_AUTHENTICATION_SUCCESS_BREAK = 200;

    /**
     * @return bool
     */
    public function init(): bool
    {
        $typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(
            VersionNumberUtility::getCurrentTypo3Version()
        );
        $this->currentTypo3Version = (int)$typo3VersionArray['version_main'];

        $this->extConfig = SiteConfigUtility::getAllConstants();
        // @extensionScannerIgnoreLine
        $provider = GeneralUtility::_GP('tx_nssociallogin_pi1')['provider'] ?? '';
        $this->provider = htmlspecialchars($provider);
        $this->hybridStorageSession = new Session();

        if ($this->provider != '') {
            $this->hybridStorageSession->set('provider', $this->provider);
            $this->hybridStorageSession->set('endPointProvider', $this->provider);
        }
        return parent::init();
    }

    /**
     * Initializes authentication for this service.
     *
     * @param string $subType: Subtype for authentication (either "getUserFE" or "getUserBE")
     * @param array $loginData: Login data submitted by user and preprocessed by AbstractUserAuthentication
     * @param array $authenticationInformation: Additional TYPO3 information for authentication services (unused here)
     * @param AbstractUserAuthentication $parentObject Calling object
     */
    public function initAuth($subType, $loginData, $authenticationInformation, $parentObject): void
    {
        try {
            $this->authUtility = GeneralUtility::makeInstance(AuthUtility::class);
        } catch (\Exception $e) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($e->getMessage(), __FILE__ . ' Line No. ' . __LINE__);
            die;
        }
        parent::initAuth($subType, $loginData, $authenticationInformation, $parentObject);
    }

    /**
     * Find usergroup records
     *
     * @return bool User informations
     * @throws IllegalFileExtensionException
     * @throws InsufficientFileWritePermissionsException
     * @throws InsufficientFolderAccessPermissionsException
     * @throws InsufficientFolderWritePermissionsException
     * @throws InsufficientUserPermissionsException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function getUser()
    {
        $user = false;
        $fileObject = null;
        // then grab the user profile
        $this->provider = $this->hybridStorageSession->get('provider') ?? '';
        if ($this->provider != '') {
            //get user
            $hybridUser = $this->authUtility->authenticate($this->provider);
            if ($hybridUser) {
                $hashedPassword = md5(uniqid('', true));
                try {
                    $hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)
                        ->getDefaultHashInstance('FE');
                    $hashedPassword = $hashInstance->getHashedPassword(uniqid('', true));
                } catch(InvalidPasswordHashException $e) {
                }
                //create username
                $email = isset($hybridUser->email) && $hybridUser->email !== '' ? $hybridUser->email : $hybridUser->emailVerified;
                $username = isset($email) && $email !== '' ? $email : $this->cleanData($hybridUser->displayName, true);
                $name = isset($hybridUser->displayName) && $hybridUser->displayName !== '' ? $this->cleanData($hybridUser->displayName) : '';
                $firstName = isset($hybridUser->firstName) && $hybridUser->firstName !== '' ? $this->cleanData($hybridUser->firstName) : '';
                $lastName = isset($hybridUser->lastName) && $hybridUser->lastName !== '' ? $this->cleanData($hybridUser->lastName) : '';
                $telephone = isset($hybridUser->phone) && $hybridUser->phone !== '' ? $this->cleanData($hybridUser->phone) : '';
                $address = isset($hybridUser->address) && $hybridUser->address !== '' ? $this->cleanData($hybridUser->address) : '';
                $city = isset($hybridUser->city) && $hybridUser->city !== '' ? $this->cleanData($hybridUser->city) : '';
                $zip = isset($hybridUser->zip) && $hybridUser->zip !== '' ? $this->cleanData($hybridUser->zip) : '';
                $country = isset($hybridUser->country) && $hybridUser->country !== '' ? $this->cleanData($hybridUser->country) : '';
                $fields = [
                    'pid' => (int)$this->extConfig['storage_page'],
                    'lastlogin' => time(),
                    'crdate' => time(),
                    'tstamp' => time(),
                    'username' => $username,
                    'name' => $name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => $hashedPassword,
                    'email' => $hybridUser->email ? $this->cleanData($hybridUser->email):'',
                    'telephone' => $telephone,
                    'address' => $address,
                    'city' => $city,
                    'zip' => $zip,
                    'country' => $country,
                    'tx_ns_social_login_identifier' => $this->cleanData($hybridUser->identifier),
                    'tx_ns_social_login_source' => 1,
                ];
                //remove null values but keep 0
                $fields = array_filter($fields, 'strlen');
                //grab image
                if (isset($hybridUser->photoURL) && $hybridUser->photoURL !== '') {
                    $uniqueName = strtolower($this->provider . '_' . $hybridUser->identifier) . '.jpg';
                    $fileContent = GeneralUtility::getUrl($hybridUser->photoURL);
                    if ($fileContent) {
                        $fileStoragePid = $this->extConfig['file_storage'];
                        $filePath = $this->extConfig['avatar_image'];

                        //this default UID is the “file-admin/“ storage, auto-created by default
                        $storagePid = $fileStoragePid ? (int)$fileStoragePid : 1;

                        $storagePath = $filePath ?? 'user_upload';
                        /* @var $storage ResourceStorage */
                        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
                        $storage = $storageRepository->findByUid($storagePid);
                        if ($storage->hasFolder($storagePath)) {
                            /* @var $fileObject AbstractFile */
                            $fileObject = $storage->createFile($uniqueName, $storage->getFolder($storagePath));
                            $storage->setFileContents($fileObject, $fileContent);
                            $fields['image'] = $fileObject->getUid();
                        }
                    }
                }

                //if the user exists in the TYPO3 database
                $exist = $this->userExist($hybridUser->identifier);

                $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionForTable('fe_users');
                if (!empty($exist)) {
                    $new = false;
                    $connection->update('fe_users', $fields, ['uid' => (int)$exist['uid']]);
                    $userUid = $exist['uid'];
                } else {
                    //get default user group
                    $fields['usergroup'] = (int)$this->extConfig['usergroup'];
                    $new = true;
                    $connection->insert('fe_users', $fields);
                    $userUid = $connection->lastInsertId('fe_users');
                }
                $uniqueUsername = $this->getUnique($username, $userUid);

                if ($uniqueUsername !== $username) {
                    $connection->update('fe_users', ['username' => $uniqueUsername], ['uid' => (int)$userUid]);
                }

                $user = $this->getUserInfos($userUid);
                //create fileReference if needed
                if ($new === true || (($new === false && $user['image'] === 0) && $fileObject !== null)) {
                    $this->createFileReferenceFromFalFileObject($fileObject, $userUid);
                }
                $user['new'] = $new;
                $user['fromHybrid'] = true;
                if (isset($user['username'])) {
                    $this->login['uname'] = $user['username'];
                }
            }
        }

        return $user;
    }

    /**
     * Authenticate user
     * @param array $user
     * @return int One of these values: 100 = Pass, 0 = Failed, 200 = Success
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function authUser(array $user): int
    {
        $this->provider = $this->hybridStorageSession->get('provider') ?? '';
        if (!$user['fromHybrid']) {
            return self::STATUS_AUTHENTICATION_FAILURE_CONTINUE;
        }
        $result = self::STATUS_AUTHENTICATION_FAILURE_CONTINUE;
        if (!empty($user) && $this->authUtility !== null && $this->authUtility->isConnectedWithProvider($this->provider)) {
            $result = self::STATUS_AUTHENTICATION_SUCCESS_BREAK;
        }

        return $result;
    }

    /**
     * Returns TRUE if single sign on for the given provider is enabled in ext_conf and is available
     *
     * @return bool
     */
    protected function isServiceAvailable(): bool
    {
        return (boolean)$this->extConfig[strtolower($this->provider) . '_enable'];
    }

    /**
     * Returns current provider
     *
     * @return string
     */
    public function getCurrentProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param mixed $identifier
     */
    protected function userExist($identifier)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $queryBuilder->select('uid')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter(
                        (int)$this->extConfig['storage_page'],
                        Connection::PARAM_INT
                    )
                ),
                $queryBuilder->expr()->eq(
                    'tx_ns_social_login_source',
                    $queryBuilder->createNamedParameter(
                        1,
                        Connection::PARAM_INT
                    )
                ),
                $queryBuilder->expr()->like(
                    'tx_ns_social_login_identifier',
                    $queryBuilder->createNamedParameter($identifier, Connection::PARAM_STR)
                )
            )
            ->orderBy('tstamp', 'DESC');

        if ($this->currentTypo3Version >= 11) {
            $res = $queryBuilder->executeQuery()->fetchAssociative();
        } else {
            $res = $queryBuilder->execute()->fetch();
        }

        return $res;
    }

    /**
     * Get user infos
     * @param int $uid
     * @return array
     */
    protected function getUserInfos(int $uid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $queryBuilder->select('*')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter(
                        (int)$this->extConfig['storage_page'],
                        Connection::PARAM_INT
                    )
                )
            );
        if ($this->currentTypo3Version >= 11) {
            $res = $queryBuilder->executeQuery()->fetchAssociative();
        } else {
            $res = $queryBuilder->execute()->fetch();
        }
        return $res;
    }

    /**
     * Create file reference for fe_user
     *
     * @param FileInterface $file
     * @param int $userUid
     */
    protected function createFileReferenceFromFalFileObject(FileInterface $file, int $userUid): void
    {
        if (is_array($file)) {
            $fileUid = $file['uid'];
        } else {
            $fileUid = $file->getUid();
        }
        $fields = [
            'pid' => (int)$this->extConfig['file_storage'],
            'crdate' => time(),
            'tstamp' => time(),
            'uid_local' => $fileUid,
            'tablenames' => 'fe_users',
            'uid_foreign' => $userUid,
            'fieldname' => 'image',
        ];

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_reference');
        $connection->insert(
            'sys_file_reference',
            $fields,
            [
                Connection::PARAM_INT,
                Connection::PARAM_INT,
                Connection::PARAM_INT,
                Connection::PARAM_INT,
                Connection::PARAM_STR,
                Connection::PARAM_INT,
                Connection::PARAM_STR,
            ]
        );
    }

    /**
     * Clean Data
     *
     * @param string $str
     * @param bool $forUsername
     * @return string
     */
    protected function cleanData(string $str, $forUsername = false): string
    {
        $str = strip_tags($str);
        //Remove extra spaces
        $str = preg_replace('/\s{2,}/', ' ', $str);
        //delete space end & begin
        $str = trim($str);
        if (mb_check_encoding($str, 'UTF-8') === false) {
            $str = utf8_encode($str);
        }

        if ($forUsername === true) {
            $str = str_replace(' ', '', $str);
            $str = mb_strtolower($str, 'utf-8');
        }

        return $str;
    }

    /**
     * @param string $username
     * @param int $id
     * @return string
     */
    protected function getUnique(string $username, int $id): string
    {
        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $username = $dataHandler->getUnique('fe_users', 'username', $username, $id, $this->extConfig['file_storage']);

        return $username;
    }
}
