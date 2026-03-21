<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Fluid namespace override',
    'description' => '',
    'category' => 'Example Extensions',
    'state' => 'stable',
    'version' => '14.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.2.0',
            'fluid_test' => '*',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
