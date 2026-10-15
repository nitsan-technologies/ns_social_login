<?php

declare(strict_types=1);

namespace NITSAN\NsSocialLogin\Form\Container;

use NITSAN\NsSocialLogin\Backend\PremiumStrip;
use TYPO3\CMS\Backend\Form\Container\FlexFormNoTabsContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Core single-sheet FlexForm container with the premium strip above the Social Login plugin options.
 */
final class PremiumStripFlexFormNoTabsContainer extends FlexFormNoTabsContainer
{
    public function render(): array
    {
        $resultArray = parent::render();

        $strip = GeneralUtility::makeInstance(PremiumStrip::class)->render();
        if ($strip !== '') {
            $resultArray['html'] = $strip . $resultArray['html'];
            $resultArray['stylesheetFiles'][] = PremiumStrip::STYLESHEET;
        }

        return $resultArray;
    }
}
