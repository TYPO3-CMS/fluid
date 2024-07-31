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

namespace TYPO3\CMS\Fluid\Tests\Functional\ViewHelpers\Form;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class TextareaViewHelperTest extends FunctionalTestCase
{
    public static function renderDataProvider(): array
    {
        return [
            'renderCorrectlySetsTagName' => [
                '<f:form.textarea />',
                [],
                '<textarea name=""></textarea>',
            ],
            'renderCorrectlySetsNameAttributeAndContent' => [
                '<f:form.textarea name="NameOfTextarea" value="Current value" />',
                [],
                '<textarea name="NameOfTextarea">Current value</textarea>',
            ],
            'renderEscapesTextareaContent' => [
                '<f:form.textarea name="NameOfTextarea" value="some <tag> & \"quotes\"" />',
                [],
                '<textarea name="NameOfTextarea">some &lt;tag&gt; &amp; &quot;quotes&quot;</textarea>',
            ],
            'renderAddsPlaceholder' => [
                '<f:form.textarea name="NameOfTextarea" placeholder="SomePlaceholder" />',
                [],
                '<textarea placeholder="SomePlaceholder" name="NameOfTextarea"></textarea>',
            ],

            // "readonly" is registered as string argument, which should lead to its
            // value being passed directly to the tag as string.
            'renderAddsReadonly' => [
                '<f:form.textarea name="NameOfTextarea" readonly="foo" />',
                [],
                '<textarea readonly="foo" name="NameOfTextarea"></textarea>',
            ],
            'renderAddsReadonly0' => [
                '<f:form.textarea name="NameOfTextarea" readonly="0" />',
                [],
                '<textarea readonly="0" name="NameOfTextarea"></textarea>',
            ],
            'renderAddsReadonly1' => [
                '<f:form.textarea name="NameOfTextarea" readonly="1" />',
                [],
                '<textarea readonly="1" name="NameOfTextarea"></textarea>',
            ],
            'renderAddsReadonlyTrue' => [
                '<f:form.textarea name="NameOfTextarea" readonly="{var}" />',
                ['var' => true],
                '<textarea readonly="1" name="NameOfTextarea"></textarea>',
            ],
            'renderAddsReadonlyFalse' => [
                '<f:form.textarea name="NameOfTextarea" readonly="{var}" />',
                ['var' => false],
                '<textarea readonly="" name="NameOfTextarea"></textarea>',
            ],
            'renderAddsReadonlyNull' => [
                '<f:form.textarea name="NameOfTextarea" readonly="{var}" />',
                ['var' => null],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
            'renderAddsReadonlyUndefined' => [
                '<f:form.textarea name="NameOfTextarea" readonly="{var}" />',
                [],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
            // Fluid removes empty string arguments that are used as tag attributes,
            // so the resulting tag has no "readonly" attribute
            'renderAddsEmptyReadonly' => [
                '<f:form.textarea name="NameOfTextarea" readonly="" />',
                [],
                '<textarea name="NameOfTextarea"></textarea>',
            ],

            // "required" is registered as bool argument, which should lead to
            // the resulting attribute being present or not based on the passed value
            'renderAddsRequired' => [
                '<f:form.textarea name="NameOfTextarea" required="true" />',
                [],
                '<textarea name="NameOfTextarea" required="required"></textarea>',
            ],
            'renderAddsRequired0' => [
                '<f:form.textarea name="NameOfTextarea" required="0" />',
                [],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
            'renderAddsRequired1' => [
                '<f:form.textarea name="NameOfTextarea" required="1" />',
                [],
                '<textarea name="NameOfTextarea" required="required"></textarea>',
            ],
            'renderAddsRequiredTrue' => [
                '<f:form.textarea name="NameOfTextarea" required="{var}" />',
                ['var' => true],
                '<textarea name="NameOfTextarea" required="required"></textarea>',
            ],
            'renderAddsRequiredFalse' => [
                '<f:form.textarea name="NameOfTextarea" required="{var}" />',
                ['var' => false],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
            'renderAddsRequiredNull' => [
                '<f:form.textarea name="NameOfTextarea" required="{var}" />',
                ['var' => null],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
            'renderAddsRequiredUndefined' => [
                '<f:form.textarea name="NameOfTextarea" required="{var}" />',
                [],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
            'renderAddsEmptyRequired' => [
                '<f:form.textarea name="NameOfTextarea" required="" />',
                [],
                '<textarea name="NameOfTextarea"></textarea>',
            ],
        ];
    }

    #[DataProvider('renderDataProvider')]
    #[Test]
    public function render(string $template, array $variables, string $expected): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($template);
        $serverRequest = (new ServerRequest())->withAttribute('extbase', new ExtbaseRequestParameters());
        $context->setRequest(new Request($serverRequest));
        $view = new TemplateView($context);
        $view->assignMultiple($variables);
        self::assertSame($expected, $view->render());
    }

    #[Test]
    public function renderCallsSetErrorClassAttribute(): void
    {
        // Create an extbase request that contains mapping results of the form object property we're working with.
        $mappingResult = new Result();
        $objectResult = $mappingResult->forProperty('myObjectName');
        $propertyResult = $objectResult->forProperty('someProperty');
        $propertyResult->addError(new Error('invalidProperty', 2));
        $extbaseRequestParameters = new ExtbaseRequestParameters();
        $extbaseRequestParameters->setOriginalRequestMappingResults($mappingResult);
        $psr7Request = (new ServerRequest())->withAttribute('extbase', $extbaseRequestParameters)
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $GLOBALS['TYPO3_REQUEST'] = $psr7Request;
        $extbaseRequest = new Request($psr7Request);

        $formObject = new \stdClass();
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<f:form object="{formObject}" fieldNamePrefix="myFieldPrefix" objectName="myObjectName"><f:form.textarea property="someProperty" errorClass="myError" /></f:form>');
        $context->setRequest($extbaseRequest);
        $view = new TemplateView($context);
        $view->assign('formObject', $formObject);
        // The point is that 'class="myError"' is added since the form had mapping errors for this property.
        self::assertStringContainsString('<textarea name="myFieldPrefix[myObjectName][someProperty]" class="myError"></textarea>', $view->render());
    }
}
