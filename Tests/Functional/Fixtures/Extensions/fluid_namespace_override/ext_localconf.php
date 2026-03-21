<?php

// Extend existing 3rd-party namespace (from ext_localconf.php)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['thirdparty_legacy'] ??= [];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['thirdparty_legacy'][] = 'TYPO3Tests\\FluidNamespaceOverride\\ExtLocalconf';
