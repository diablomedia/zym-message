<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->files()
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRiskyAllowed(true)
    ->setRules(array(
        '@PSR2'                     => true,
        '@PHPUnit60Migration:risky' => true,
        'binary_operator_spaces'    => ['operators' => ['=' => 'align', '=>' => 'align']],
        'single_quote'              => true,
        'array_syntax'              => ['syntax' => 'short'],
        'concat_space'              => ['spacing' => 'one'],
        'psr_autoloading'           => ['dir' => 'src'],
    ))
    ->setUsingCache(true)
    ->setFinder($finder);
