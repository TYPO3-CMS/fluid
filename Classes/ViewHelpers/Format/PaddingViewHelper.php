<?php

/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Formats a string using PHPs str_pad function.
 * @See http://www.php.net/manual/en/function.str_pad.php
 *
 * = Examples =
 *
 * <code title="Defaults">
 * <f:format.padding padLength="10">TYPO3</f:format.padding>
 * </code>
 * <output>
 * TYPO3     (note the trailing whitespace)
 * <output>
 *
 * <code title="Specify padding string">
 * <f:format.padding padLength="10" padString="-=">TYPO3</f:format.padding>
 * </code>
 * <output>
 * TYPO3-=-=-
 * </output>
 *
 * <code title="Specify padding string and padding type">
 * <f:format.padding padLength="10" padString="-" padType="both">TYPO3</f:format.padding>
 * </code>
 * <output>
 * --TYPO3---
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class Tx_Fluid_ViewHelpers_Format_PaddingViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Format the arguments with the given printf format string.
	 *
	 * @param integer $padLength Length of the resulting string. If the value of pad_length is negative or less than the length of the input string, no padding takes place.
	 * @param string $padString The padding string
	 * @param string $padType The padding type ("right", "left" or "both")
	 * @return string The formatted value
	 * @author Bastian Waidelich <bastian@typo3.org>
	 * @api
	 */
	public function render($padLength, $padString = ' ', $padType = '') {
		$string = $this->renderChildren();
		switch (strtoupper($padType)) {
			case 'LEFT':
				$padding = STR_PAD_LEFT;
				break;
			case 'BOTH':
				$padding = STR_PAD_BOTH;
				break;
			case 'RIGHT':
			default:
				$padding = STR_PAD_RIGHT;
		}
		return str_pad($string, $padLength, $padString, $padding);
	}
}
?>