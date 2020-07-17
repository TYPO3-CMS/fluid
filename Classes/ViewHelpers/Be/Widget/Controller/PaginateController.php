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

namespace TYPO3\CMS\Fluid\ViewHelpers\Be\Widget\Controller;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController;

/**
 * Class PaginateController
 */
class PaginateController extends AbstractWidgetController
{
    /**
     * @var array
     */
    protected $configuration = ['itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'recordsLabel' => ''];

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected $objects;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var int
     */
    protected $numberOfPages = 1;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $itemsPerPage = 0;

    /**
     * @var int
     */
    protected $numberOfObjects = 0;

    /**
     * Initializes necessary variables for all actions.
     */
    public function initializeAction()
    {
        $this->objects = $this->widgetConfiguration['objects'];
        ArrayUtility::mergeRecursiveWithOverrule($this->configuration, $this->widgetConfiguration['configuration'], false);
        $this->numberOfObjects = count($this->objects);
        $itemsPerPage = (int)$this->configuration['itemsPerPage'];
        $this->numberOfPages = $itemsPerPage > 0 ? (int)ceil($this->numberOfObjects / $itemsPerPage) : 0;
    }

    /**
     * @param int $currentPage
     */
    public function indexAction($currentPage = 1)
    {
        // set current page
        $this->currentPage = max((int)$currentPage, 1);
        $this->currentPage = min($this->numberOfPages, $this->currentPage);

        if ($this->currentPage > $this->numberOfPages) {
            // set $modifiedObjects to NULL if the page does not exist
            // (happens when numberOfPages is zero, not having any items)
            $modifiedObjects = null;
        } else {
            // modify query
            $this->itemsPerPage = (int)$this->configuration['itemsPerPage'];
            $query = $this->objects->getQuery();
            $query->setLimit($this->itemsPerPage);
            $this->offset = $this->itemsPerPage * ($this->currentPage - 1);
            if ($this->currentPage > 1) {
                $query->setOffset($this->offset);
            }
            $modifiedObjects = $query->execute();
        }
        $this->view->assign('contentArguments', [
            $this->widgetConfiguration['as'] => $modifiedObjects
        ]);
        $this->view->assign('configuration', $this->configuration);
        $this->view->assign('pagination', $this->buildPagination());
    }

    /**
     * Returns an array with the keys "current", "numberOfPages", "nextPage", "previousPage", "startRecord", "endRecord"
     *
     * @return array
     */
    protected function buildPagination()
    {
        $endRecord = $this->offset + $this->itemsPerPage;
        if ($endRecord > $this->numberOfObjects) {
            $endRecord = $this->numberOfObjects;
        }
        $pagination = [
            'current' => $this->currentPage,
            'numberOfPages' => $this->numberOfPages,
            'hasLessPages' => $this->currentPage > 1,
            'hasMorePages' => $this->currentPage < $this->numberOfPages,
            'startRecord' => $this->offset + 1,
            'endRecord' => $endRecord
        ];
        if ($this->currentPage < $this->numberOfPages) {
            $pagination['nextPage'] = $this->currentPage + 1;
        }
        if ($this->currentPage > 1) {
            $pagination['previousPage'] = $this->currentPage - 1;
        }
        return $pagination;
    }
}
