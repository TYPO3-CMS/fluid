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

namespace TYPO3\CMS\Fluid\Tests\Functional\ViewHelpers\Be\Security;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

final class IfAuthenticatedViewHelperTest extends FunctionalTestCase
{
    protected bool $initializeDatabase = false;

    #[Test]
    public function viewHelperRendersThenChildIfBeUserIsLoggedIn(): void
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['uid' => 1];
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource(
            '<f:be.security.ifAuthenticated><f:then>then child</f:then><f:else>else child</f:else></f:be.security.ifAuthenticated>'
        );
        self::assertEquals('then child', (new TemplateView($context))->render());
    }

    #[Test]
    public function viewHelperRendersElseChildIfBeUserIsNotLoggedIn(): void
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['uid' => 0];
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource(
            '<f:be.security.ifAuthenticated><f:then>then child</f:then><f:else>else child</f:else></f:be.security.ifAuthenticated>'
        );
        self::assertEquals('else child', (new TemplateView($context))->render());
    }
}
