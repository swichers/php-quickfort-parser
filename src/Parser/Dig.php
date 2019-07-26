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
     * @return boolean
     *   Returns true if the blueprint is a dig command.
     */
    public function checkHeader(): bool
    {
        $header = $this->blueprintHeader;
        if ($header['command'] !== 'dig') {
            return false;
        }

        return true;
    }
}
