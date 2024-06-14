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

namespace TYPO3\CMS\Fluid\Tests\Functional\ViewHelpers\Uri;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class ResourceViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    #[Test]
    public function renderingFailsWithNonExtSyntaxWithoutExtensionNameWithPsr7Request()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1639672666);
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<f:uri.resource path="Icons/Extension.svg" />');
        $context->setRequest(new ServerRequest());
        (new TemplateView($context))->render();
    }

    #[Test]
    public function renderingFailsWhenExtensionNameNotSetInExtbaseRequest(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<f:uri.resource path="Icons/Extension.svg" />');
        $serverRequest = (new ServerRequest())->withAttribute('extbase', new ExtbaseRequestParameters());
        $context->setRequest(new Request($serverRequest));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1640097205);
        (new TemplateView($context))->render();
    }

    public static function renderWithoutRequestDataProvider(): \Generator
    {
        yield 'render returns URI using UpperCamelCase extensionName' => [
            '<f:uri.resource path="Icons/Extension.svg" extensionName="Core" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
        yield 'render returns URI using extension key as extensionName' => [
            '<f:uri.resource path="Icons/Extension.svg" extensionName="core" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
        yield 'render returns URI using EXT: syntax' => [
            '<f:uri.resource path="EXT:core/Resources/Public/Icons/Extension.svg" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
    }

    #[DataProvider('renderWithoutRequestDataProvider')]
    #[Test]
    public function render(string $template, string $expected): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($template);
        self::assertSame($expected, (new TemplateView($context))->render());
    }

    public static function renderWithExtbaseRequestDataProvider(): \Generator
    {
        yield 'render returns URI using extensionName from Extbase Request' => [
            '<f:uri.resource path="Icons/Extension.svg" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
        yield 'render gracefully trims leading slashes from path' => [
            '<f:uri.resource path="/Icons/Extension.svg" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
        yield 'render returns URI using UpperCamelCase extensionName' => [
            '<f:uri.resource path="Icons/Extension.svg" extensionName="Core" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
        yield 'render returns URI using extension key as extensionName' => [
            '<f:uri.resource path="Icons/Extension.svg" extensionName="core" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
        yield 'render returns URI using EXT: syntax' => [
            '<f:uri.resource path="EXT:core/Resources/Public/Icons/Extension.svg" />',
            'typo3/sysext/core/Resources/Public/Icons/Extension.svg?' . filemtime('typo3/sysext/core/Resources/Public/Icons/Extension.svg'),
        ];
    }

    #[DataProvider('renderWithExtbaseRequestDataProvider')]
    #[Test]
    public function renderWithExtbaseRequest(string $template, string $expected): void
    {
        $extbaseRequestParameters = new ExtbaseRequestParameters();
        $extbaseRequestParameters->setControllerExtensionName('Core');
        $serverRequest = (new ServerRequest())->withAttribute('extbase', $extbaseRequestParameters);
        $extbaseRequest = (new Request($serverRequest));
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($template);
        $context->setRequest($extbaseRequest);
        self::assertEquals($expected, (new TemplateView($context))->render());
    }

    #[Test]
    public function renderWithoutCacheBusting(): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource('<f:uri.resource useCacheBusting="false" path="Icons/Extension.svg" extensionName="core" />');
        self::assertSame('typo3/sysext/core/Resources/Public/Icons/Extension.svg', (new TemplateView($context))->render());
    }
}
