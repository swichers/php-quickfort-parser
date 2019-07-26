<?php declare(strict_types=1);

namespace QuickFort\Parser;

/**
 * Interface BlueprintParserInterface.
 *
 * Defines standard parser methods to use.
 */
interface BlueprintParserInterface
{

    /**
     * Sets the blueprint text to use.
     *
     * Blueprint text is expected to be a block of CSV formatted text with
     * newlines as delimiters.
     *
     * ```csv
     * #dig
     * d,d,d,#
     * ~,i,d,#
     * d,d,d,#
     * #,#,#,#
     * ```
     *
     * @param string $blueprintText The new blueprint text to use.
     *
     * @return void
     */
    public function setBlueprint(string $blueprintText);

    /**
     * Get the processed blueprint layers.
     *
     * @return array[]
     *   A nested array of processed blueprint layers.
     */
    public function getLayers(): array;

    /**
     * Get blueprint header information.
     *
     * Return value:
     *
     *   'command' => string|null.
     *   'comment' => string|null.
     *   'start' => array['x' => int, 'y' => int].
     *
     * @return array
     *   A key-value array of header information.
     */
    public function getHeader(): array;

    /**
     * Get the blueprint text.
     *
     * @return string
     *   The blueprint text.
     */
    public function getBlueprint(): string;
}
