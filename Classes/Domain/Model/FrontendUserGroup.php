<?php

namespace NITSAN\NsSocialLogin\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * FrontendUserGroup
 */
class FrontendUserGroup
{
    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected ObjectStorage $subgroup;

    /**
     * Constructs a new Frontend User Group
     *
     * @param string $title
     */
    public function __construct(string $title = '')
    {
        $this->setTitle($title);
        $this->subgroup = new ObjectStorage();
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
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param ObjectStorage<FrontendUserGroup>
     * @return void
     */
    public function setSubgroup(ObjectStorage $subgroup): void
    {
        $this->subgroup = $subgroup;
    }

    /**
     * @param FrontendUserGroup $subgroup
     * @return void
     */
    public function addSubgroup(FrontendUserGroup $subgroup): void
    {
        $this->subgroup->attach($subgroup);
    }

    /**
     * @param FrontendUserGroup $subgroup
     * @return void
     */
    public function removeSubgroup(FrontendUserGroup $subgroup): void
    {
        $this->subgroup->detach($subgroup);
    }

    /**
     * @return ObjectStorage<FrontendUserGroup> An object storage containing the subgroups
     */
    public function getSubgroup(): ObjectStorage
    {
        return $this->subgroup;
    }
}
