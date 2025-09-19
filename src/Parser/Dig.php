<?php declare(strict_types=1);

namespace QuickFort\Parser;

/**
 * A QuickFort blueprint parser for 'dig' blueprints.
 *
 * This class extends the BlueprintParserBase to provide functionality specific
 * to 'dig' blueprints.
 *
 * @package QuickFort\Parser
 */
class Dig extends BlueprintParserBase
{
    /**
     * Checks if the blueprint header is valid for a 'dig' blueprint.
     *
     * @return bool True if the blueprint is a 'dig' blueprint, false
     *              otherwise.
     */
    public function checkHeader(): bool
    {
        $command = $this->blueprintHeader['command'] ?? null;
        return $command === 'dig';
    }
}
