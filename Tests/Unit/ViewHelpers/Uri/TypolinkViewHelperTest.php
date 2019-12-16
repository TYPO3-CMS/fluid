<?php
namespace TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\Uri;

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

use TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * Class TypolinkViewHelperTest
 */
class TypolinkViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @return array
     */
    public function typoScriptConfigurationData()
    {
        return [
            'empty input' => [
                '', // input from link field
                '', // additional parameters from fluid
                '', //expected typolink
            ],
            'simple id input' => [
                19,
                '',
                '19',
            ],
            'external url with target' => [
                'www.web.de _blank',
                '',
                'www.web.de _blank',
            ],
            'page with class' => [
                '42 - css-class',
                '',
                '42 - css-class',
            ],
            'page with title' => [
                '42 - - "a link title"',
                '',
                '42 - - "a link title"',
            ],
            'page with title and parameters' => [
                '42 - - "a link title" &x=y',
                '',
                '42 - - "a link title" &x=y',
            ],
            'page with title and extended parameters' => [
                '42 - - "a link title" &x=y',
                '&a=b',
                '42 - - "a link title" &x=y&a=b',
            ],
            'only page id and overwrite' => [
                '42',
                '&a=b',
                '42 - - - &a=b',
            ],
            't3:// with class' => [
                't3://url?url=https://example.org?param=1&other=dude - css-class',
                '',
                't3://url?url=https://example.org?param=1&other=dude - css-class',
            ],
            't3:// with title' => [
                't3://url?url=https://example.org?param=1&other=dude - - "a link title"',
                '',
                't3://url?url=https://example.org?param=1&other=dude - - "a link title"',
            ],
            't3:// with title and parameters' => [
                't3://url?url=https://example.org?param=1&other=dude - - "a link title" &x=y',
                '',
                't3://url?url=https://example.org?param=1&other=dude - - "a link title" &x=y',
            ],
            't3:// with title and extended parameters' => [
                't3://url?url=https://example.org?param=1&other=dude - - "a link title" &x=y',
                '&a=b',
                't3://url?url=https://example.org?param=1&other=dude - - "a link title" &x=y&a=b',
            ],
            't3:// and overwrite' => [
                't3://url?url=https://example.org?param=1&other=dude',
                '&a=b',
                't3://url?url=https://example.org?param=1&other=dude - - - &a=b',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider typoScriptConfigurationData
     * @param string $input
     * @param string $additionalParametersFromFluid
     * @param string $expected
     * @throws \InvalidArgumentException
     */
    public function createTypolinkParameterFromArgumentsReturnsExpectedArray($input, $additionalParametersFromFluid, $expected)
    {
        /** @var \TYPO3\CMS\Fluid\ViewHelpers\Uri\TypolinkViewHelper|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\TestingFramework\Core\AccessibleObjectInterface $subject */
        $subject = $this->getAccessibleMock(TypolinkViewHelper::class, ['dummy']);
        $result = $subject->_call('createTypolinkParameterFromArguments', $input, $additionalParametersFromFluid);
        $this->assertSame($expected, $result);
    }
}
