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
use TYPO3\CMS\Fluid\Core\Widget\WidgetContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class WidgetContextTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Fluid\Core\Widget\WidgetContext
     */
    protected $widgetContext;

    /**

     */
    protected function setUp()
    {
        $this->widgetContext = new \TYPO3\CMS\Fluid\Core\Widget\WidgetContext();
    }

    /**
     * @test
     * @dataProvider getSetterGetterTestValues
     * @param string $name
     * @param mixed $value
     */
    public function getterMethodReturnsValue($name, $value)
    {
        $property = new \ReflectionProperty(WidgetContext::class, $name);
        $property->setAccessible(true);
        $property->setValue($this->widgetContext, $value);
        $method = 'get' . ucfirst($name);
        $this->assertEquals($value, call_user_func_array([$this->widgetContext, $method], []));
    }

    /**
     * @test
     * @dataProvider getSetterGetterTestValues
     * @param string $name
     * @param mixed $value
     */
    public function setterMethodSetsPropertyValue($name, $value)
    {
        $method = 'set' . ucfirst($name);
        call_user_func_array([$this->widgetContext, $method], [$value]);
        $this->assertAttributeEquals($value, $name, $this->widgetContext);
    }

    /**
     * @return array
     */
    public function getSetterGetterTestValues()
    {
        return [
            ['parentPluginNamespace', 'foo-bar'],
            ['parentExtensionName', 'baz'],
            ['parentPluginName', 'baz-foo'],
            ['widgetViewHelperClassName', 'bar-foo'],
        ];
    }

    /**
     * @test
     */
    public function widgetIdentifierCanBeReadAgain()
    {
        $this->widgetContext->setWidgetIdentifier('myWidgetIdentifier');
        $this->assertEquals('myWidgetIdentifier', $this->widgetContext->getWidgetIdentifier());
    }

    /**
     * @test
     */
    public function ajaxWidgetIdentifierCanBeReadAgain()
    {
        $this->widgetContext->setAjaxWidgetIdentifier(42);
        $this->assertEquals(42, $this->widgetContext->getAjaxWidgetIdentifier());
    }

    /**
     * @test
     */
    public function widgetConfigurationCanBeReadAgain()
    {
        $this->widgetContext->setWidgetConfiguration(['key' => 'value']);
        $this->assertEquals(['key' => 'value'], $this->widgetContext->getWidgetConfiguration());
    }

    /**
     * @test
     */
    public function controllerObjectNameCanBeReadAgain()
    {
        $this->widgetContext->setControllerObjectName('Tx_Fluid_Object_Name');
        $this->assertEquals('Tx_Fluid_Object_Name', $this->widgetContext->getControllerObjectName());
    }

    /**
     * @test
     */
    public function viewHelperChildNodesCanBeReadAgain()
    {
        $viewHelperChildNodes = $this->createMock(\TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\RootNode::class);
        $renderingContext = $this->createMock(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface::class);
        $this->widgetContext->setViewHelperChildNodes($viewHelperChildNodes, $renderingContext);
        $this->assertSame($viewHelperChildNodes, $this->widgetContext->getViewHelperChildNodes());
        $this->assertSame($renderingContext, $this->widgetContext->getViewHelperChildNodeRenderingContext());
    }

    /**
     * @test
     */
    public function sleepReturnsExpectedPropertyNames()
    {
        $this->assertEquals(
            [
                'widgetIdentifier', 'ajaxWidgetIdentifier', 'widgetConfiguration', 'controllerObjectName',
                'parentPluginNamespace', 'parentVendorName', 'parentExtensionName', 'parentPluginName',
                'widgetViewHelperClassName'
            ],
            $this->widgetContext->__sleep()
        );
    }
}
