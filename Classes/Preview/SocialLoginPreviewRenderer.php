<?php

declare(strict_types=1);

namespace NITSAN\NsSocialLogin\Preview;

use NITSAN\NsSocialLogin\Backend\PremiumStrip;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Page module preview of the Social Login plugin: premium strip above the core plugin preview.
 */
final class SocialLoginPreviewRenderer extends StandardContentPreviewRenderer
{
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $content = parent::renderPageModulePreviewContent($item);

        $strip = GeneralUtility::makeInstance(PremiumStrip::class)->render();
        if ($strip === '') {
            return $content;
        }

        GeneralUtility::makeInstance(PageRenderer::class)->addCssFile(PremiumStrip::STYLESHEET);

        return $strip . $content;
    }
}
