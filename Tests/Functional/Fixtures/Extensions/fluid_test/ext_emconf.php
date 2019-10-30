<?php
$EM_CONF[$_EXTKEY] = [
  'title' => 'Extension skeleton for TYPO3 7',
  'description' => 'Description for ext',
  'category' => 'Example Extensions',
  'author' => 'Helmut Hummel',
  'author_email' => 'info@helhum.io',
  'author_company' => 'helhum.io',
  'shy' => '',
  'priority' => '',
  'module' => '',
  'state' => 'stable',
  'internal' => '',
  'uploadfolder' => '0',
  'createDirs' => '',
  'modify_tables' => '',
  'clearCacheOnLoad' => 0,
  'lockType' => '',
  'version' => '8.7.30',
  'constraints' =>
  [
    'depends' =>
    [
      'typo3' => '8.7.30',
    ],
    'conflicts' =>
    [
    ],
    'suggests' =>
    [
    ],
  ],
  'autoload' =>
  [
    'psr-4' =>
    [
      'TYPO3Fluid\\FluidTest\\' => 'Classes',
    ],
  ],
  'autoload-dev' =>
  [
    'psr-4' =>
    [
      'TYPO3Fluid\\FluidTest\\Tests\\' => 'Tests',
    ],
  ],
];
