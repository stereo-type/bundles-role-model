<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
                ->in(__DIR__)
                ->exclude('vendor')
                ->exclude('var')
                ->exclude('node_modules');

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        'cast_spaces' => ['space' => 'none'],
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'no_trailing_comma_in_singleline' => true,
        '@PSR12' => true,
    ])
    ->setFinder($finder);
