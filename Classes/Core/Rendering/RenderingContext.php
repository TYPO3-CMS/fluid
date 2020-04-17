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

namespace TYPO3\CMS\Fluid\Core\Rendering;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Cache\FluidTemplateCache;
use TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperResolver;
use TYPO3\CMS\Fluid\View\TemplatePaths;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\Configuration;
use TYPO3Fluid\Fluid\Core\Parser\InterceptorInterface;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Class RenderingContext
 */
class RenderingContext extends \TYPO3Fluid\Fluid\Core\Rendering\RenderingContext
{
    /**
     * Controller context being passed to the ViewHelper
     *
     * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext|null
     */
    protected $controllerContext;

    /**
     * @var string
     */
    protected $controllerName = 'Default';

    /**
     * @var string
     */
    protected $controllerAction = 'Default';

    /**
     * @param \TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer $viewHelperVariableContainer
     */
    public function injectViewHelperVariableContainer(ViewHelperVariableContainer $viewHelperVariableContainer)
    {
        $this->viewHelperVariableContainer = $viewHelperVariableContainer;
    }

    /**
     * @param ViewInterface $view
     */
    public function __construct(ViewInterface $view = null)
    {
        if ($view !== null) {
            // Note: if $view is received here this indicates internal framework instancing
            // and it is safe to call the parent constructor. Custom, non-view-providing
            // usages will only perform the initialisation below (which is sufficient mind you!)
            parent::__construct($view);
        } else {
            // Reproduced partial initialisation from parent::__construct; minus the custom
            // implementations we attach below.
            $this->setTemplateParser(new TemplateParser($this));
            if (method_exists($this, 'setTemplateCompiler')) {
                $this->setTemplateCompiler(new TemplateCompiler());
            }
            if (method_exists($this, 'setViewHelperInvoker')) {
                $this->setViewHelperInvoker(new ViewHelperInvoker());
            }
            $this->setViewHelperVariableContainer(new ViewHelperVariableContainer());
            $this->setVariableProvider(new StandardVariableProvider());
        }

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        if (method_exists($this, 'setTemplateProcessors')) {
            $this->setTemplateProcessors(array_map([$objectManager, 'get'], $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['preProcessors']));
        }
        $this->setExpressionNodeTypes($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['expressionNodeTypes']);
        $this->setTemplatePaths($objectManager->get(TemplatePaths::class));
        $this->setViewHelperResolver($objectManager->get(ViewHelperResolver::class));

        if (method_exists($this, 'setCache')) {
            /** @var FluidTemplateCache $cache */
            $cache = $objectManager->get(CacheManager::class)->getCache('fluid_template');
            if (is_a($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fluid_template']['frontend'], FluidTemplateCache::class, true)) {
                $this->setCache($cache);
            }
        }
    }

    /**
     * Alternative to buildParserConfiguration, called only in Fluid 3.0
     *
     * @return Configuration
     */
    public function getParserConfiguration(): Configuration
    {
        $parserConfiguration = parent::getParserConfiguration();
        $this->addInterceptorsToParserConfiguration($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['interceptors'], $parserConfiguration);
        return $parserConfiguration;
    }

    /**
     * Build parser configuration
     *
     * @return Configuration
     * @throws \InvalidArgumentException if a class not implementing InterceptorInterface was registered
     */
    public function buildParserConfiguration()
    {
        $parserConfiguration = parent::buildParserConfiguration();
        $this->addInterceptorsToParserConfiguration($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['interceptors'], $parserConfiguration);
        return $parserConfiguration;
    }

    protected function addInterceptorsToParserConfiguration(iterable $interceptors, Configuration $parserConfiguration): void
    {
        foreach ($interceptors as $className) {
            $interceptor = GeneralUtility::makeInstance($className);
            if (!$interceptor instanceof InterceptorInterface) {
                throw new \InvalidArgumentException('Interceptor "' . $className . '" needs to implement ' . InterceptorInterface::class . '.', 1462869795);
            }
            $parserConfiguration->addInterceptor($interceptor);
        }
    }

    /**
     * Get the controller context which will be passed to the ViewHelper
     *
     * @return \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext The controller context to set
     */
    public function getControllerContext()
    {
        return $this->controllerContext;
    }

    /**
     * @param string $action
     */
    public function setControllerAction($action)
    {
        $dotPosition = strpos($action, '.');
        if ($dotPosition !== false) {
            $action = substr($action, 0, $dotPosition);
        }
        $this->controllerAction = $action;
        if ($this->controllerContext) {
            $this->controllerContext->getRequest()->setControllerActionName(lcfirst($action));
        }
    }

    /**
     * @param string $controllerName
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidControllerNameException
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
        if ($this->controllerContext) {
            $this->controllerContext->getRequest()->setControllerName($controllerName);
        }
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerContext ? $this->controllerContext->getRequest()->getControllerName() : $this->controllerName;
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return $this->controllerContext ? $this->controllerContext->getRequest()->getControllerActionName() : $this->controllerAction;
    }

    /**
     * Set the controller context which will be passed to the ViewHelper
     *
     * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext The controller context to set
     */
    public function setControllerContext(ControllerContext $controllerContext)
    {
        $request = $controllerContext->getRequest();
        $this->controllerContext = $controllerContext;
        $this->setControllerAction($request->getControllerActionName());
        // Check if Request is using a sub-package key; in which case we translate this
        // for our RenderingContext as an emulated plain old sub-namespace controller.
        $controllerName = $request->getControllerName();
        if ($request->getControllerSubpackageKey() && !strpos($controllerName, '\\')) {
            $this->setControllerName($request->getControllerSubpackageKey() . '\\' . $controllerName);
        } else {
            $this->setControllerName($controllerName);
        }
    }
}
