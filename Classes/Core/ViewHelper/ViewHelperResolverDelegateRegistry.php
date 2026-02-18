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

namespace TYPO3\CMS\Fluid\Core\ViewHelper;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use TYPO3\CMS\Fluid\Core\Component\ComponentCollectionRegistry;
use TYPO3\CMS\Fluid\Core\Component\DeclarativeComponentCollection;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolverDelegateInterface;

/**
 * @internal May change / vanish any time
 */
#[Autoconfigure(public: true)]
final readonly class ViewHelperResolverDelegateRegistry
{
    /**
     * @var array<string, ViewHelperResolverDelegateInterface>
     */
    private array $viewHelperResolverDelegates;

    public function __construct(
        ComponentCollectionRegistry $componentCollectionRegistry,
        #[AutowireIterator('fluid.resolverdelegate', exclude: [DeclarativeComponentCollection::class], indexAttribute: 'identifier')]
        iterable $resolverDelegates,
    ) {
        $this->viewHelperResolverDelegates = array_replace(
            iterator_to_array($resolverDelegates),
            $componentCollectionRegistry->getAll(),
        );
    }

    /**
     * @return array<string, ViewHelperResolverDelegateInterface>
     */
    public function getAll(): array
    {
        return $this->viewHelperResolverDelegates;
    }
}
