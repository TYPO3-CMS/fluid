<?php
namespace TYPO3\CMS\Fluid\Tests\Unit\View;

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
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\View\AbstractTemplateView;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\TestingFramework\Fluid\Unit\Core\Rendering\RenderingContextFixture;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

/**
 * Test case
 */
class AbstractTemplateViewTest extends UnitTestCase
{
    /**
     * @var AbstractTemplateView|AccessibleObjectInterface
     */
    protected $view;

    /**
     * @var RenderingContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderingContext;

    /**
     * @var ViewHelperVariableContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewHelperVariableContainer;

    /**
     * Sets up this test case
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewHelperVariableContainer = $this->getMockBuilder(ViewHelperVariableContainer::class)
            ->setMethods(['setView'])
            ->getMock();
        $this->renderingContext = $this->getMockBuilder(RenderingContextFixture::class)
            ->setMethods(['getViewHelperVariableContainer'])
            ->getMock();
        $this->renderingContext->expects(self::any())->method('getViewHelperVariableContainer')->will(self::returnValue($this->viewHelperVariableContainer));
        $this->view = $this->getAccessibleMock(AbstractTemplateView::class, ['dummy'], [], '', false);
        $this->view->setRenderingContext($this->renderingContext);
    }

    /**
     * @test
     */
    public function viewIsPlacedInViewHelperVariableContainer()
    {
        $this->viewHelperVariableContainer->expects(self::once())->method('setView')->with($this->view);
        $this->view->setRenderingContext($this->renderingContext);
    }
}
