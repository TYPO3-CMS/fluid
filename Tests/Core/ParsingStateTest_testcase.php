<?php

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package Fluid
 * @subpackage Tests
 * @version $Id$
 */
/**
 * Testcase for ParsingState
 *
 * @package Fluid
 * @subpackage Tests
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
require_once(t3lib_extMgm::extPath('extbase', 'Tests/Base_testcase.php'));
class Tx_Fluid_Core_ParsingStateTest_testcase extends Tx_Extbase_Base_testcase {

	/**
	 * Parsing state
	 * @var Tx_Fluid_Core_ParsingState
	 */
	protected $parsingState;
	
	public function setUp() {
		$this->parsingState = new Tx_Fluid_Core_ParsingState();
	}
	public function tearDown() {
		unset($this->parsingState);
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function test_setRootNodeCanBeReadOutAgain() {
		$rootNode = new Tx_Fluid_Core_SyntaxTree_RootNode();
		$this->parsingState->setRootNode($rootNode);
		$this->assertSame($this->parsingState->getRootNode(), $rootNode, 'Root node could not be read out again.');
	}
	
	/**
	 * @test
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 */
	public function test_pushAndGetFromStackWorks() {
		$rootNode = new Tx_Fluid_Core_SyntaxTree_RootNode();
		$this->parsingState->pushNodeToStack($rootNode);
		$this->assertSame($rootNode, $this->parsingState->getNodeFromStack($rootNode), 'Node returned from stack was not the right one.');
		$this->assertSame($rootNode, $this->parsingState->popNodeFromStack($rootNode), 'Node popped from stack was not the right one.');
	}
}



?>
