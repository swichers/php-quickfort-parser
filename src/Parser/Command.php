<?php declare(strict_types=1);

namespace QuickFort\Parser;

/**
 * Class Command
 *
 * Parses a command string and provides methods to help use it.
 */
class Command
{

    /**
     * The command.
     *
     * @var string
     */
    protected $command;

    /**
     * A keyed array (x,y) of expansion data.
     *
     * @var array
     */
    protected $expansion;

    /**
     * Command constructor.
     *
     * Expects a simple command like 'd' or an expansion syntax like 'd(3x3)'.
     *
     * @param string $text Text to parse into command data.
     */
    public function __construct(string $text)
    {
        $normalized_text = $this->normalizeText($text);
        $this->parseTextToCommand($normalized_text);
    }

    /**
     * Normalize the given text.
     *
     * @param string $text The text to normalize.
     *
     * @return string
     *   The normalized text.
     */
    protected function normalizeText(string $text): string
    {
        return strtolower(trim($text));
    }

    /**
     * Parses text into command data.
     *
     * @param string $text The text to parse into command data.
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

        if (strpos($text, '(') !== false) {
            $parts = explode('(', trim($text, ')'));
            $xy_values = explode('x', $parts[1]);
            $this->command = $parts[0];
            $this->expansion = [
                'x' => $xy_values[0],
                'y' => $xy_values[1],
            ];
        }
    }

    /**
     * Check if the command is an up layer navigation.
     *
     * @return boolean
     *   Returns true if the command moves up a layer.
     */
    public function isLayerUp(): bool
    {
        return $this->command == '#<';
    }

    /**
     * Check if the command is a down layer navigation.
     *
     * @return boolean
     *   Returns true if the command moves down a layer.
     */
    public function isLayerDown(): bool
    {
        return $this->command == '#>';
    }

    /**
     * Check if the command is allowed.
     *
     * @return boolean
     *   Returns true if the command is an allowed command.
     */
    public function isAllowedCommand(): bool
    {
        $commands = 'djuihrx';

        return in_array($this->command, str_split($commands));
    }

    /**
     * Check if the command results in no operation.
     *
     * @return boolean
     *   Returns true if there is no operation to perform.
     */
    public function isNoOp(): bool
    {
        $noops = '#~`';

        return in_array($this->command, str_split($noops));
    }

    /**
     * Check if the command was a comment.
     *
     * @return boolean
     *   Returns true if the command is a comment.
     */
    public function isComment(): bool
    {
        return $this->command === '#';
    }

    /**
     * Get the formatted command string.
     *
     * Will include expansion information.
     *
     * @return string
     *   The formatted command string.
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
     * Check if the command has expansion information.
     *
     * @return boolean
     *   Returns true if the command has worthwhile expansion information.
     */
    public function hasExpansion(): bool
    {
        return $this->expansion['x'] > 1 || $this->expansion['y'] > 1;
    }

    /**
     * Gets command expansion information.
     *
     * @return array[]
     *   A key-value array of x and y data.
     */
    public function getExpansion(): array
    {
        return $this->expansion;
    }

    /**
     * Get the command.
     *
     * Will not return expansion information.
     *
     * @return string
     *   The command text.
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Return the command as a string.
     *
     * @return string
     *   The command.
     */
    public function __toString(): string
    {
        return $this->command;
    }
}
