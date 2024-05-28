<?php

namespace NITSAN\NsSocialLogin\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * User
 */
class User
{
    /**
     * source
     *
     * @var int
     */
    protected int $source = 0;

    /**
     * identifier
     *
     * @var string
     */
    protected string $identifier = '';

    /**
     * @var string
     */
    protected string $username = '';

    /**
     * @var string
     */
    protected string $password = '';

    /**
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected ObjectStorage $usergroup;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    protected string $firstName = '';

    /**
     * @var string
     */
    protected string $middleName = '';

    /**
     * @var string
     */
    protected string $lastName = '';

    /**
     * @var string
     */
    protected string $address = '';

    /**
     * @var string
     */
    protected string $telephone = '';

    /**
     * @var string
     */
    protected string $fax = '';

    /**
     * @var string
     */
    protected string $email = '';

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $zip = '';

    /**
     * @var string
     */
    protected string $city = '';

    /**
     * @var string
     */
    protected string $country = '';

    /**
     * @var string
     */
    protected string $www = '';

    /**
     * @var string
     */
    protected string $company = '';

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $image;

    /**
     * @var \DateTime|null
     */
    protected ?\DateTime $lastlogin;

    /**
     * Constructs a new Front-End User
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username = '', string $password = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->usergroup = new ObjectStorage();
        $this->image = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject()
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
        $this->image = $this->image ?? new ObjectStorage();
    }

    /**
     * @param string $username
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param ObjectStorage<FrontendUserGroup> $usergroup
     * @return void
     */
    public function setUsergroup(ObjectStorage $usergroup): void
    {
        $this->usergroup = $usergroup;
    }

    /**
     * @param FrontendUserGroup $usergroup
     * @return void
     */
    public function addUsergroup(FrontendUserGroup $usergroup): void
    {
        $this->usergroup->attach($usergroup);
    }

    /**
     * @param FrontendUserGroup $usergroup
     * @return void
     */
    public function removeUsergroup(FrontendUserGroup $usergroup): void
    {
        $this->usergroup->detach($usergroup);
    }

    /**
     * @return ObjectStorage<FrontendUserGroup>
     */
    public function getUsergroup(): ObjectStorage
    {
        return $this->usergroup;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $middleName
     * @return void
     */
    public function setMiddleName(string $middleName): void
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $address
     * @return void
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $telephone
     * @return void
     */
    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @param string $fax
     * @return void
     */
    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $zip
     * @return void
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $city
     * @return void
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $country
     * @return void
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $www
     * @return void
     */
    public function setWww(string $www): void
    {
        $this->www = $www;
    }

    /**
     * @return string
     */
    public function getWww(): string
    {
        return $this->www;
    }

    /**
     * @param string $company
     * @return void
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param ObjectStorage<FileReference> $image
     * @return void
     */
    public function setImage(ObjectStorage $image): void
    {
        $this->image = $image;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getImage(): ObjectStorage
    {
        return $this->image;
    }

    /**
     * @param \DateTime $lastlogin
     * @return void
     */
    public function setLastlogin(\DateTime $lastlogin): void
    {
        $this->lastlogin = $lastlogin;
    }

    /**
     * @return \DateTime
     */
    public function getLastlogin()
    {
        return $this->lastlogin;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param int $source
     */
    public function setSource(int $source): void
    {
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getSource(): int
    {
        return $this->source;
    }
}
