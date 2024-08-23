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

namespace TYPO3\CMS\Fluid\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class EscapeChildrenRenderingTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    protected array $testExtensionsToLoad = ['typo3/sysext/fluid/Tests/Functional/Fixtures/Extensions/fluid_test'];

    public static function viewHelperTemplateSourcesDataProvider(): array
    {
        return [
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with children, properly encodes variable value' =>
            [
                '<ft:escapeChildrenEnabledAndEscapeOutputDisabled>{settings.test}</ft:escapeChildrenEnabledAndEscapeOutputDisabled>',
                '&lt;strong&gt;Bla&lt;/strong&gt;',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with children, properly encodes variable value' =>
            [
                '{settings.test -> ft:escapeChildrenEnabledAndEscapeOutputDisabled()}',
                '&lt;strong&gt;Bla&lt;/strong&gt;',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with argument, does not encode variable value' =>
            [
                '<ft:escapeChildrenEnabledAndEscapeOutputDisabled content="{settings.test}" />',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with argument, does not encode variable value' =>
            [
                '{ft:escapeChildrenEnabledAndEscapeOutputDisabled(content: settings.test)}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with string, does not encode string value' =>
            [
                '{ft:escapeChildrenEnabledAndEscapeOutputDisabled(content: \'<strong>Bla</strong>\')}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Inline syntax with argument in quotes, does not encode variable value' =>
            [
                '{ft:escapeChildrenEnabledAndEscapeOutputDisabled(content: \'{settings.test}\')}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and children rendering, does not encode variable value' =>
            [
                '<ft:escapeChildrenEnabledAndEscapeOutputDisabled content="{settings.test -> ft:escapeChildrenEnabledAndEscapeOutputDisabled()}" />',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenEnabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and argument in inline, does not encode variable value' =>
            [
                '<ft:escapeChildrenEnabledAndEscapeOutputDisabled content="{ft:escapeChildrenEnabledAndEscapeOutputDisabled(content: settings.test)}" />',
                '<strong>Bla</strong>',
            ],

            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with children, properly encodes variable value' =>
            [
                '<ft:escapeChildrenDisabledAndEscapeOutputDisabled>{settings.test}</ft:escapeChildrenDisabledAndEscapeOutputDisabled>',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with children, properly encodes variable value' =>
            [
                '{settings.test -> ft:escapeChildrenDisabledAndEscapeOutputDisabled()}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with argument, does not encode variable value' =>
            [
                '<ft:escapeChildrenDisabledAndEscapeOutputDisabled content="{settings.test}" />',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with argument, does not encode variable value' =>
            [
                '{ft:escapeChildrenDisabledAndEscapeOutputDisabled(content: settings.test)}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with string, does not encode string value' =>
            [
                '{ft:escapeChildrenDisabledAndEscapeOutputDisabled(content: \'<strong>Bla</strong>\')}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Inline syntax with argument in quotes, does not encode variable value' =>
            [
                '{ft:escapeChildrenDisabledAndEscapeOutputDisabled(content: \'{settings.test}\')}',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and children rendering, does not encode variable value' =>
            [
                '<ft:escapeChildrenDisabledAndEscapeOutputDisabled content="{settings.test -> ft:escapeChildrenDisabledAndEscapeOutputDisabled()}" />',
                '<strong>Bla</strong>',
            ],
            'EscapeChildrenDisabledAndEscapeOutputDisabled: Tag syntax with nested inline syntax and argument in inline, does not encode variable value' =>
            [
                '<ft:escapeChildrenDisabledAndEscapeOutputDisabled content="{ft:escapeChildrenDisabledAndEscapeOutputDisabled(content: settings.test)}" />',
                '<strong>Bla</strong>',
            ],

        ];
    }

    #[DataProvider('viewHelperTemplateSourcesDataProvider')]
    #[Test]
    public function renderingTest(string $viewHelperTemplate, string $expectedOutput): void
    {
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($viewHelperTemplate);
        $context->getViewHelperResolver()->addNamespace('ft', 'TYPO3Tests\\FluidTest\\ViewHelpers');
        $view = new TemplateView($context);
        $view->assign('settings', ['test' => '<strong>Bla</strong>']);
        self::assertSame($expectedOutput, $view->render());
    }
}
