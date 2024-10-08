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

namespace TYPO3\CMS\Fluid\ViewHelpers;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Messaging\FlashMessageRendererResolver;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3Fluid\Fluid\Core\Variables\ScopedVariableProvider;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper which renders the flash messages (if there are any) as an unsorted list.
 *
 * In case you need custom Flash Message HTML output, please write your own ViewHelper for the moment.
 *
 * Examples
 * ========
 *
 * Simple
 * ------
 *
 * ::
 *
 *    <f:flashMessages />
 *
 * A list of flash messages.
 *
 * TYPO3 core style
 * ----------------
 *
 * ::
 *
 *    <f:flashMessages />
 *
 * Output::
 *
 *    <div class="typo3-messages">
 *       <div class="alert alert-info">
 *          <div class="alert-inner">
 *             <div class="alert-icon">
 *                <span class="icon-emphasized">
 *                   <span class="t3js-icon icon icon-size-small icon-state-default icon-actions-info" data-identifier="actions-info">
 *                      <span class="icon-markup">
 *                         <svg class="icon-color"><use xlink:href="/typo3/sysext/core/Resources/Public/Icons/T3Icons/sprites/actions.svg#actions-info"></use></svg>
 *                      </span>
 *                   </span>
 *                </span>
 *             </div>
 *             <div class="alert-content">
 *                <div class="alert-title">Info - Title for Info message</div>
 *                <p class="alert-message">Message text here.</p>
 *             </div>
 *          </div>
 *       </div>
 *    </div>
 *
 * Output flash messages as a description list
 * -------------------------------------------
 *
 * ::
 *
 *    <f:flashMessages as="flashMessages">
 *       <dl class="messages">
 *          <f:for each="{flashMessages}" as="flashMessage">
 *             <dt>{flashMessage.code}</dt>
 *             <dd>{flashMessage.message}</dd>
 *          </f:for>
 *       </dl>
 *    </f:flashMessages>
 *
 * Output::
 *
 *    <dl class="messages">
 *       <dt>1013</dt>
 *       <dd>Some Warning Message.</dd>
 *   </dl>
 *
 * Using a specific queue
 * ----------------------
 *
 * ::
 *
 *    <f:flashMessages queueIdentifier="myQueue" />
 */
final class FlashMessagesViewHelper extends AbstractViewHelper
{
    /**
     * ViewHelper outputs HTML therefore output escaping has to be disabled
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('queueIdentifier', 'string', 'Flash-message queue to use');
        $this->registerArgument('as', 'string', 'The name of the current flashMessage variable for rendering inside');
    }

    /**
     * Renders FlashMessages and flushes the FlashMessage queue
     *
     * Note: This does not disable the current page cache in order to prevent FlashMessage output
     *       from being cached.
     *       In case of conditional flash message rendering, caching must be disabled
     *       (e.g. for a controller action).
     *       Custom caching using the Caching Framework can be used in this case.
     *
     * @return mixed
     */
    public function render()
    {
        $as = $this->arguments['as'];
        $queueIdentifier = $this->arguments['queueIdentifier'];
        if ($queueIdentifier === null) {
            if (!$this->renderingContext->hasAttribute(ServerRequestInterface::class)
                || !$this->renderingContext->getAttribute(ServerRequestInterface::class) instanceof RequestInterface) {
                // Throw if not an extbase request
                throw new \RuntimeException(
                    'ViewHelper f:flashMessages needs an extbase Request object to resolve the Queue identifier magically.'
                    . ' When not in extbase context, set attribute "queueIdentifier".',
                    1639821269
                );
            }
            $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
            $extensionService = GeneralUtility::makeInstance(ExtensionService::class);
            $pluginNamespace = $extensionService->getPluginNamespace($request->getControllerExtensionName(), $request->getPluginName());
            $queueIdentifier = 'extbase.flashmessages.' . $pluginNamespace;
        }
        $flashMessageQueue = GeneralUtility::makeInstance(FlashMessageService::class)->getMessageQueueByIdentifier($queueIdentifier);
        $flashMessages = $flashMessageQueue->getAllMessagesAndFlush();
        if (count($flashMessages) === 0) {
            return '';
        }
        if ($as === null) {
            return GeneralUtility::makeInstance(FlashMessageRendererResolver::class)->resolve()->render($flashMessages);
        }
        $variableProvider = new ScopedVariableProvider($this->renderingContext->getVariableProvider(), new StandardVariableProvider([$as => $flashMessages]));
        $this->renderingContext->setVariableProvider($variableProvider);
        $content = $this->renderChildren();
        $this->renderingContext->setVariableProvider($variableProvider->getGlobalVariableProvider());
        return $content;
    }
}
