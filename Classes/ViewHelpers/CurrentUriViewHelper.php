<?php

namespace NITSAN\NsSocialLogin\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CurrentUriViewHelper extends AbstractViewHelper
{
    /**
     * render
     *
     * @return string
     */
    public function render(): string
    {
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        return $url;
    }
}
