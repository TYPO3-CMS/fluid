<?php
namespace TYPO3\CMS\Fluid\Tests\Unit\Core\Widget;

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

use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Response;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\Core\Widget\WidgetRequestBuilder;
use TYPO3\CMS\Fluid\Core\Widget\WidgetRequestHandler;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class WidgetRequestHandlerTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Fluid\Core\Widget\WidgetRequestHandler
     */
    protected $widgetRequestHandler;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->widgetRequestHandler = $this->getAccessibleMock(\TYPO3\CMS\Fluid\Core\Widget\WidgetRequestHandler::class, ['dummy'], [], '', false);
    }

    /**
     * @test
     */
    public function canHandleRequestReturnsTrueIfCorrectGetParameterIsSet()
    {
        $_GET['fluid-widget-id'] = 123;
        self::assertTrue($this->widgetRequestHandler->canHandleRequest());
    }

    /**
     * @test
     */
    public function canHandleRequestReturnsFalsefGetParameterIsNotSet()
    {
        $_GET['some-other-id'] = 123;
        self::assertFalse($this->widgetRequestHandler->canHandleRequest());
    }

    /**
     * @test
     */
    public function priorityIsHigherThanDefaultRequestHandler()
    {
        $defaultWebRequestHandler = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Web\AbstractRequestHandler::class)
            ->setMethods(['handleRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        self::assertTrue($this->widgetRequestHandler->getPriority() > $defaultWebRequestHandler->getPriority());
    }

    /**
     * @test
     */
    public function handleRequestCallsExpectedMethods()
    {
        $handler = new WidgetRequestHandler();
        $request = $this->createMock(Request::class);
        $requestBuilder = $this->getMockBuilder(WidgetRequestBuilder::class)
            ->setMethods(['build'])
            ->getMock();
        $requestBuilder->expects(self::once())->method('build')->willReturn($request);
        $objectManager = $this->prophesize(ObjectManager::class);
        $handler->injectObjectManager($objectManager->reveal());
        $objectManager->get(\Prophecy\Argument::any())->willReturn($this->createMock(Response::class));
        $requestDispatcher = $this->getMockBuilder(Dispatcher::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();
        $requestDispatcher->expects(self::once())->method('dispatch')->with($request);
        $this->inject($handler, 'widgetRequestBuilder', $requestBuilder);
        $this->inject($handler, 'dispatcher', $requestDispatcher);
        $handler->handleRequest();
    }
}
