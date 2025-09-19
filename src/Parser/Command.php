<?php declare(strict_types=1);

namespace QuickFort\Parser;

use QuickFort\Enums\DigCommands;
use QuickFort\Enums\LayerCommands;

/**
 * Represents a command in a QuickFort blueprint.
 *
 * This class parses a command string, which can be a simple command like 'd'
 * or a command with an expansion like 'd(3x3)'. It provides methods to inspect
 * the command and its properties.
 *
 * @package QuickFort\Parser
 */
class Command
{
    /**
     * The command string, without any expansion data.
     *
     * @var string
     */
    protected string $command;

    /**
     * An array containing the expansion data for the command.
     *
     * The array has two keys: 'x' and 'y', representing the expansion in the
     * x and y directions.
     *
     * @var array{x: int, y: int}
     */
    protected array $expansion;

    /**
     * Command constructor.
     *
     * @param string $text The command string to parse. This can be a simple
     *                     command like 'd' or a command with an expansion
     *                     like 'd(3x3)'.
     */
    public function __construct(string $text)
    {
        $normalized_text = $this->normalizeText($text);
        $this->parseTextToCommand($normalized_text);
    }

    /**
     * Normalizes the given command text.
     *
     * This method converts the command text to lowercase and trims whitespace.
     *
     * @param string $text The command text to normalize.
     *
     * @return string The normalized command text.
     */
    protected function normalizeText(string $text): string
    {
        return strtolower(trim($text));
    }

    /**
     * Parses the normalized command text into its constituent parts.
     *
     * This method sets the `command` and `expansion` properties based on the
     * given command text.
     *
     * @param string $text The normalized command text to parse.
     *
     * @return void
     */
    protected function parseTextToCommand(string $text): void
    {
        $this->command = $text;
        $this->expansion = [
            'x' => 1,
            'y' => 1,
        ];

        if (str_contains($text, '(')) {
            $this->parseTextWithExpansion($text);
        }
    }

    /**
     * Parses command text that contains an expansion marker.
     *
     * For example, 'd(2x3)' would be parsed into the command 'd' and the
     * expansion array ['x' => 2, 'y' => 3].
     *
     * @param string $text The command text with an expansion to parse.
     *
     * @return void
     */
    protected function parseTextWithExpansion(string $text): void
    {
        $parts = explode('(', trim($text, ')'));
        $xy_values = explode('x', $parts[1]);
        $this->command = $parts[0];
        $this->expansion = [
            'x' => (int) $xy_values[0],
            'y' => (int) $xy_values[1],
        ];
    }

    /**
     * Checks if the command is a layer up command.
     *
     * @return bool True if the command is a layer up command, false otherwise.
     */
    public function isLayerUp(): bool
    {
        return $this->command === LayerCommands::UP->value;
    }

    /**
     * Checks if the command is a layer down command.
     *
     * @return bool True if the command is a layer down command, false otherwise.
     */
    public function isLayerDown(): bool
    {
        return $this->command === LayerCommands::DOWN->value;
    }

    /**
     * Checks if the command is an allowed dig command.
     *
     * @return bool True if the command is an allowed dig command, false
     *              otherwise.
     */
    public function isAllowedCommand(): bool
    {
        return DigCommands::tryFrom($this->command) !== null;
    }

    /**
     * Checks if the command is a no-op.
     *
     * No-op commands are characters that are ignored by the parser.
     *
     * @return bool True if the command is a no-op, false otherwise.
     */
    public function isNoOp(): bool
    {
        $noops = '#~`';

        return in_array($this->command, str_split($noops), true);
    }

    /**
     * Checks if the command is a comment.
     *
     * @return bool True if the command is a comment, false otherwise.
     */
    public function isComment(): bool
    {
        return $this->command === '#';
    }

    /**
     * Gets the formatted command string, including expansion information.
     *
     * For example, a dig command with an expansion of 2x3 would be returned as
     * 'd(2x3)'.
     *
     * @return string The formatted command string.
     */
    public function getFormatted(): string
    {
        if (!$this->hasExpansion()) {
            return $this->command;
        }

        $expansion = $this->getExpansion();

        return sprintf(
            '%s(%dx%d)',
            $this->getCommand(),
            $expansion['x'],
            $expansion['y']
        );
    }

    /**
     * Checks if the command has an expansion.
     *
     * An expansion is considered to be present if the expansion in the x or y
     * direction is greater than 1.
     *
     * @return bool True if the command has an expansion, false otherwise.
     */
    public function hasExpansion(): bool
    {
        return $this->expansion['x'] > 1 || $this->expansion['y'] > 1;
    }

    /**
     * Gets the command expansion information.
     *
     * @return array{x: int, y: int} A key-value array of x and y data.
     */
    public function getExpansion(): array
    {
        return $this->expansion;
    }

    /**
     * Gets the command string, without any expansion information.
     *
     * @return string The command string.
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Returns the command as a string.
     *
     * This is an alias for getCommand().
     *
     * @return string The command string.
     */
    public function __toString(): string
    {
        return $this->command;
    }
}
