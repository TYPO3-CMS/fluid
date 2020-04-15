<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Fluid\ViewHelpers\Uri;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Service\TypoLinkCodecService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * A ViewHelper to create uris from fields supported by the link wizard.
 *
 * Example
 * =======
 *
 * ``{link}`` contains ``19 - - - &X=y``
 *
 * Please note that due to the nature of typolink you have to provide a full
 * set of parameters.
 * If you use the parameter only, then target, class and title will be discarded.
 *
 * Minimal usage
 * -------------
 *
 * ::
 *
 *    <f:uri.typolink parameter="{link}" />
 *
 * ``/page/path/name.html?X=y``
 *
 * Depending on routing and page path configuration.
 *
 * Full parameter usage
 * --------------------
 *
 * ::
 *
 *    <f:uri.typolink parameter="{link}" additionalParams="&u=b" />
 *
 * ``/page/path/name.html?X=y&u=b``
 *
 * Depending on routing and page path configuration.
 */
class TypolinkViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('parameter', 'string', 'stdWrap.typolink style parameter string', true);
        $this->registerArgument('additionalParams', 'string', 'stdWrap.typolink additionalParams', false, '');
        $this->registerArgument('language', 'string', 'link to a specific language - defaults to the current language, use a language ID or "current" to enforce a specific language', false, null);
        // @deprecated useCacheHash
        $this->registerArgument('useCacheHash', 'bool', 'Deprecated: You should not need this.', false);
        $this->registerArgument('addQueryString', 'bool', '', false, false);
        $this->registerArgument('addQueryStringMethod', 'string', '', false, 'GET');
        $this->registerArgument('addQueryStringExclude', 'string', '', false, '');
        $this->registerArgument('absolute', 'bool', 'Ensure the resulting URL is an absolute URL', false, false);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if (isset($arguments['useCacheHash'])) {
            trigger_error('Using the argument "noCacheHash" in <f:uri.typolink> ViewHelper has no effect anymore. Remove the argument in your fluid template, as it will result in a fatal error.', E_USER_DEPRECATED);
        }
        $parameter = $arguments['parameter'];

        $typoLinkCodec = GeneralUtility::makeInstance(TypoLinkCodecService::class);
        $typoLinkConfiguration = $typoLinkCodec->decode($parameter);
        $mergedTypoLinkConfiguration = static::mergeTypoLinkConfiguration($typoLinkConfiguration, $arguments);
        $typoLinkParameter = $typoLinkCodec->encode($mergedTypoLinkConfiguration);

        $content = '';
        if ($parameter) {
            $content = static::invokeContentObjectRenderer($arguments, $typoLinkParameter);
        }
        return $content;
    }

    protected static function invokeContentObjectRenderer(array $arguments, string $typoLinkParameter): string
    {
        $addQueryString = $arguments['addQueryString'] ?? false;
        $addQueryStringMethod = $arguments['addQueryStringMethod'] ?? 'GET';
        $addQueryStringExclude = $arguments['addQueryStringExclude'] ?? '';
        $absolute = $arguments['absolute'] ?? false;

        $instructions = [
            'parameter' => $typoLinkParameter,
            'forceAbsoluteUrl' => $absolute,
        ];
        if (isset($arguments['language']) && $arguments['language'] !== null) {
            $instructions['language'] = $arguments['language'];
        }
        if ($addQueryString) {
            $instructions['addQueryString'] = $addQueryString;
            $instructions['addQueryString.'] = [
                'method' => $addQueryStringMethod,
                'exclude' => $addQueryStringExclude,
            ];
        }

        $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        return $contentObject->typoLink_URL($instructions);
    }

    /**
     * Merges view helper arguments with typolink parts.
     *
     * @param array $typoLinkConfiguration
     * @param array $arguments
     * @return array
     */
    protected static function mergeTypoLinkConfiguration(array $typoLinkConfiguration, array $arguments): array
    {
        if ($typoLinkConfiguration === []) {
            return $typoLinkConfiguration;
        }

        $additionalParameters = $arguments['additionalParams'] ?? '';

        // Combine additionalParams
        if ($additionalParameters) {
            $typoLinkConfiguration['additionalParams'] .= $additionalParameters;
        }

        return $typoLinkConfiguration;
    }
}
