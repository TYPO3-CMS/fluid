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

namespace TYPO3\CMS\Fluid\Core\Widget;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * This is the bootstrap for Ajax Widget responses
 *
 * @internal This class is only used internally by the widget framework.
 */
class Bootstrap
{
    /**
     * Back reference to the parent content object
     * This has to be public as it is set directly from TYPO3
     *
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    public $cObj;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @param string $content The content
     * @param array $configuration The TS configuration array
     * @return string $content The processed content
     */
    public function run($content, $configuration)
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->initializeConfiguration($configuration);
        $ajaxWidgetContextHolder = $this->objectManager->get(AjaxWidgetContextHolder::class);
        $widgetIdentifier = GeneralUtility::_GET('fluid-widget-id');
        $widgetContext = $ajaxWidgetContextHolder->get($widgetIdentifier);
        $configuration['extensionName'] = $widgetContext->getParentExtensionName();
        $configuration['pluginName'] = $widgetContext->getParentPluginName();
        $extbaseBootstrap = $this->objectManager->get(\TYPO3\CMS\Extbase\Core\Bootstrap::class);
        $extbaseBootstrap->cObj = $this->cObj;
        return $extbaseBootstrap->run($content, $configuration);
    }

    /**
     * Initializes the Object framework.
     *
     * @param array $configuration
     * @see initialize()
     */
    public function initializeConfiguration($configuration)
    {
        $this->configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        /** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObject */
        $contentObject = $this->cObj ?? GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->configurationManager->setContentObject($contentObject);
        $this->configurationManager->setConfiguration($configuration);
    }
}
