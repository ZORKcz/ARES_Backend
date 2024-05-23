<?php

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    ->withRootFiles()

    ->withRules([
        ArraySyntaxFixer::class,
    ])

    ->withSkip([
        // This rule switches operands e.g. $variable === 'value' to 'value' === $variable
        YodaStyleFixer::class,
        // This rule adds a space after the not operator e.g. !$variable to ! $variable
        NotOperatorWithSuccessorSpaceFixer::class,
        // This rule enforces line break before && and || operators in conditions, when split to multiple lines
        OperatorLinebreakFixer::class,
    ])

    ->withPhpCsFixerSets(
        symfony: true,
    )

    ->withPreparedSets(
        psr12: true,
        common: true
    );
