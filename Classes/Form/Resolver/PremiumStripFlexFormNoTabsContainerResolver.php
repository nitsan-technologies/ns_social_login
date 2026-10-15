<?php

declare(strict_types=1);

namespace NITSAN\NsSocialLogin\Form\Resolver;

use NITSAN\NsSocialLogin\Backend\PremiumStrip;
use NITSAN\NsSocialLogin\Form\Container\PremiumStripFlexFormNoTabsContainer;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Backend\Form\NodeResolverInterface;

/**
 * Uses PremiumStripFlexFormNoTabsContainer for the plugin options (pi_flexform) of the Social Login plugin.
 */
final class PremiumStripFlexFormNoTabsContainerResolver implements NodeResolverInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * TYPO3 v9-v12 pass the node factory and data to the constructor, v13 calls setData().
     */
    public function __construct(?NodeFactory $nodeFactory = null, array $data = [])
    {
        $this->data = $data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function resolve(): ?string
    {
        if (($this->data['tableName'] ?? '') !== 'tt_content'
            || ($this->data['fieldName'] ?? '') !== 'pi_flexform'
            || !empty($this->data['isInlineChild'])
            || (string)($this->data['recordTypeValue'] ?? '') !== 'list'
        ) {
            return null;
        }

        $listType = $this->data['databaseRow']['list_type'] ?? '';
        if (is_array($listType)) {
            $listType = reset($listType);
        }

        return (string)$listType === PremiumStrip::PLUGIN_SIGNATURE
            ? PremiumStripFlexFormNoTabsContainer::class
            : null;
    }
}
