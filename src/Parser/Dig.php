<?php declare(strict_types=1);

namespace QuickFort\Parser;

/**
 * QuickFort Dig blueprint parser implementation.
 *
 * Provides dig specific functionality for blueprint parsing.
 */
class Dig extends BlueprintParserBase
{

    /**
     * Check if the header is valid for this type of parser.
     *
     * @return bool
     *   Returns true if the blueprint is a dig command.
     */
    public function checkHeader(): bool
    {
        $command = $this->blueprintHeader['command'] ?? null;
        return $command === 'dig';
    }
}
