<?php

// Define namespace to be extended by another extension
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['thirdparty_legacy'] ??= [];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['thirdparty_legacy'][] = 'TYPO3Tests\\FluidTest\\ExtLocalconf';
