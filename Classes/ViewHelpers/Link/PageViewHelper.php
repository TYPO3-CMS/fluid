<?php

declare(strict_types=1);

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

namespace TYPO3\CMS\Fluid\ViewHelpers\Link;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * A ViewHelper for creating links to TYPO3 pages. Tailored for extbase plugins, uses extbase Request and extbase UriBuilder.
 *
 * Examples
 * ========
 *
 * Link to the current page
 * ------------------------
 *
 * ::
 *
 *    <f:link.page>page link</f:link.page>
 *
 * Output::
 *
 *    <a href="/page/path/name.html">page link</a>
 *
 * Depending on current page, routing and page path configuration.
 *
 * Query parameters
 * ----------------
 *
 * ::
 *
 *    <f:link.page pageUid="1" additionalParams="{foo: 'bar'}">page link</f:link.page>
 *
 * Output::
 *
 *    <a href="/page/path/name.html?foo=bar">page link</a>
 *
 * Depending on current page, routing and page path configuration.
 *
 * Query parameters for extensions
 * -------------------------------
 *
 * ::
 *
 *    <f:link.page pageUid="1" additionalParams="{extension_key: {foo: 'bar'}}">page link</f:link.page>
 *
 * Output::
 *
 *    <a href="/page/path/name.html?extension_key[foo]=bar">page link</a>
 *
 * Depending on current page, routing and page path configuration.
 */
final class PageViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document', false);
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter');
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.');
        $this->registerArgument('language', 'string', 'link to a specific language - defaults to the current language, use a language ID or "current" to enforce a specific language', false);
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.');
        $this->registerArgument('additionalParams', 'array', 'Additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)');
        $this->registerArgument('absolute', 'bool', 'If set, the URI of the rendered link is absolute');
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI');
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'Arguments to be removed from the URI. Only active if $addQueryString = TRUE');
    }

    public function render(): string
    {
        /** @var RenderingContext $renderingContext */
        $renderingContext = $this->renderingContext;
        $request = $renderingContext->getRequest();
        if (!$request instanceof RequestInterface) {
            throw new \RuntimeException(
                'ViewHelper f:link.page can be used only in extbase context and needs a request implementing extbase RequestInterface.',
                1639819269
            );
        }

        $pageUid = isset($this->arguments['pageUid']) ? (int)$this->arguments['pageUid'] : null;
        $pageType = isset($this->arguments['pageType']) ? (int)$this->arguments['pageType'] : 0;
        $noCache = isset($this->arguments['noCache']) && (bool)$this->arguments['noCache'];
        $section = isset($this->arguments['section']) ? (string)$this->arguments['section'] : '';
        $language = $this->arguments['language'] ?? null;
        $linkAccessRestrictedPages = isset($this->arguments['linkAccessRestrictedPages']) && (bool)$this->arguments['linkAccessRestrictedPages'];
        $additionalParams = isset($this->arguments['additionalParams']) ? (array)$this->arguments['additionalParams'] : [];
        $absolute = isset($this->arguments['absolute']) && (bool)$this->arguments['absolute'];
        $addQueryString = isset($this->arguments['addQueryString']) && (bool)$this->arguments['addQueryString'];
        $argumentsToBeExcludedFromQueryString = isset($this->arguments['argumentsToBeExcludedFromQueryString']) ? (array)$this->arguments['argumentsToBeExcludedFromQueryString'] : [];

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->reset()
            ->setRequest($request)
            ->setTargetPageType($pageType)
            ->setNoCache($noCache)
            ->setSection($section)
            ->setLanguage($language)
            ->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
            ->setArguments($additionalParams)
            ->setCreateAbsoluteUri($absolute)
            ->setAddQueryString($addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
        ;

        if (MathUtility::canBeInterpretedAsInteger($pageUid)) {
            $uriBuilder->setTargetPageUid((int)$pageUid);
        }

        $uri = $uriBuilder->build();
        if ($uri !== '') {
            $this->tag->addAttribute('href', $uri);
            $this->tag->setContent($this->renderChildren());
            $this->tag->forceClosingTag(true);
            $result = $this->tag->render();
        } else {
            $result = (string)$this->renderChildren();
        }
        return $result;
    }
}
