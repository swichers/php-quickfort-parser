<?php declare(strict_types=1);

namespace QuickFort\Parser;

/**
 * Base class for QuickFort blueprint parsers.
 *
 * This class provides a barebones implementation of a QuickFort blueprint
 * parser. It can be extended to create parsers for specific blueprint types,
 * such as 'dig' blueprints.
 *
 * @package QuickFort\Parser
 */
class BlueprintParserBase implements BlueprintParserInterface
{
    /**
     * A key-value array of header information.
     *
     * @var array{
     *     command: string|null,
     *     start: array{x: int, y: int, comment: string}|null,
     *     comment: string|null
     * }
     * @see BlueprintParserInterface::getHeader()
     */
    protected array $blueprintHeader;

    /**
     * The original, unprocessed blueprint text.
     *
     * @var string
     */
    protected string $originalBlueprint;

    /**
     * The lines of the blueprint, with the header line removed.
     *
     * @var string[]
     */
    protected array $blueprintLines;

    /**
     * BlueprintParserBase constructor.
     *
     * @param string|null $blueprintText The blueprint text to parse.
     */
    public function __construct(?string $blueprintText = null)
    {
        if (!empty($blueprintText)) {
            $this->setBlueprint($blueprintText);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setBlueprint(string $blueprintText): void
    {
        $this->originalBlueprint = $blueprintText;
        $this->blueprintLines = $this->textToLines($blueprintText);
        $this->blueprintHeader
            = $this->parseLineAsHeader($this->blueprintLines[0] ?: '');

        // We parsed a header so remove the line that contained it.
        if (!empty($this->blueprintHeader['command'])) {
            unset($this->blueprintLines[0]);
        }

        // Ensure we have some blueprint lines in case we didn't parse any.
        if (empty($this->blueprintLines)) {
            $this->blueprintLines = [];
        }
    }

    /**
     * Parses a blueprint string into individual lines.
     *
     * @param string $text The blueprint text to parse.
     *
     * @return string[] An array of blueprint lines.
     */
    protected function textToLines(string $text): array
    {
        $lines = explode(PHP_EOL, $text) ?: [];
        $lines = array_map('trim', $lines);

        return $lines ?: [];
    }

    /**
     * Parses a blueprint line for header information.
     *
     * The header line is expected to start with a '#' character, followed by
     * the command, and optional start coordinates and a comment.
     *
     * @param string $line The line to parse for header information.
     *
     * @return array An array of header information.
     * @see BlueprintParserInterface::getHeader()
     */
    protected function parseLineAsHeader(string $line): array
    {
        $header = [
            'command' => null,
            'start'   => null,
            'comment' => null,
        ];

        $line = trim($line);
        if (!str_starts_with($line, '#')) {
            return $header;
        }

        // Chop the preceding pound sign.
        $line = trim(substr($line, 1));

        // The command text can be followed by a number of different tokens.
        $matched_command_ends = [
            // Command text can be followed by a space.
            strpos($line, ' '),
            // Or a comma.
            strpos($line, ','),
            // Or nothing at all.
            strpos($line, PHP_EOL),
        ];

        // Possible for strpos above to return FALSE.
        $matched_command_ends = array_filter($matched_command_ends);
        // If there was no token found then assume the command is the only text.
        $command_end_pos = min($matched_command_ends ?: [strlen($line)]);

        $header['command'] = trim(substr($line, 0, $command_end_pos));
        $header['command'] = strtolower($header['command']);

        $line = trim(substr($line, $command_end_pos));

        // Parse out starting position information.
        if (str_starts_with($line, 'start(')) {
            $start_len = strlen('start(');
            $closing_paren_pos = strpos($line, ')');

            $start_text = substr(
                $line,
                $start_len,
                $closing_paren_pos - $start_len
            );

            list($start_x, $start_y, $start_comment) = explode(
                ';',
                $start_text
            );
            $header['start'] = [
                'x' => intval($start_x),
                'y' => intval($start_y),
                'comment' => trim($start_comment),
            ];

            $line = trim(substr($line, $closing_paren_pos + 1));
        }

        // Any remaining text is a comment.
        $header['comment'] = $line ?: '';

        // Header might be CSV formatted so let's clean the ends off.
        $header['comment'] = trim($header['comment'], ',') ?: null;

        return $header;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlueprint(): string
    {
        return $this->originalBlueprint;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(): array
    {
        return $this->blueprintHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getLayers(): array
    {
        return $this->processLines();
    }

    /**
     * Processes the blueprint lines into a nested array of layers.
     *
     * @return array<int, array<int, array<int, string>>> A nested array of
     *                                                   processed blueprint
     *                                                   layers.
     */
    protected function processLines(): array
    {
        if (empty($this->blueprintLines)) {
            return [];
        }

        // Take our simple lines and group them into their layers.
        $layers = $this->groupLinesByLayer($this->blueprintLines);
        // Reorder our layers based on layer up/down commands.
        $layers = $this->adjustLayerOrder($layers);
        // Process the layer lines into individual commands.
        $layers = $this->processLayerLines($layers);
        // Process area expansions into individual commands.
        $layers = $this->processAreaExpansions($layers);

        return $layers;
    }

    /**
     * Groups blueprint lines by the layer they belong to.
     *
     * @param string[] $lines An array of blueprint lines.
     *
     * @return array<int, array<int, string>> An array of blueprint lines,
     *                                        grouped into a nested array by
     *                                        layer.
     */
    protected function groupLinesByLayer(array $lines): array
    {
        $layers = [];
        $layers[] = [];

        foreach ($lines as $line) {
            $csv_line = str_getcsv($line);
            $command = new Command($csv_line[0]);
            // Layer shift so start a new grouping.
            if ($command->isLayerUp() || $command->isLayerDown()) {
                $layers[] = [];
            }

            // Layer grouping may have shifted above, always target the latest.
            $layer = &$layers[count($layers) - 1];
            $layer[] = $line;
        }

        return $layers;
    }

    /**
     * Reorders blueprint layers based on layer up or down commands.
     *
     * @param array<int, array<int, string>> $layers A nested array of blueprint
     *                                               layers.
     *
     * @return array<int, array<int, string>> The reordered layers.
     */
    protected function adjustLayerOrder(array $layers): array
    {
        $adjusted = [];

        foreach ($layers as $layer) {
            $csv_line = str_getcsv($layer[0]);

            $command = new Command($csv_line[0]);
            // If we have a layer shift up command and existing layers defined
            // we need to position the current layer in the previous location.
            if ($command->isLayerUp() && count($adjusted) > 0) {
                // Pop the last layer off.
                $temporary = array_slice($adjusted, 0, -1);
                // Add the current one.
                $temporary[] = $layer;
                // Re-add the original last layer.
                $temporary = array_merge($temporary, [$adjusted[count($adjusted) - 1]]);

                $adjusted = $temporary;

                continue;
            }

            $adjusted[] = $layer;
        }

        return $adjusted;
    }

    /**
     * Processes the given layer lines into individual commands.
     *
     * @param array<int, array<int, string>> $layers An array of layers and their
     *                                               lines.
     *
     * @return array<int, array<int, array<int, string>>> An array of layers and
     *                                                   their individual
     *                                                   commands.
     */
    protected function processLayerLines(array $layers): array
    {
        foreach ($layers as &$lines) {
            foreach ($lines as &$line) {
                $line = $this->parseLine($line);
            }
            unset($line);
        }
        unset($lines);

        return $layers;
    }

    /**
     * Expands command area expansions found in the given layers.
     *
     * For example, a command 'd(2x2)' will be expanded into a 2x2 grid of 'd'
     * commands.
     *
     * @param array<int, array<int, array<int, string>>> $layers The layers to
     *                                                            process for
     *                                                            area
     *                                                            expansions.
     *
     * @return array<int, array<int, array<int, string>>> The layers with their
     *                                                   area expansions
     *                                                   replaced by individual
     *                                                   commands.
     */
    protected function processAreaExpansions(array $layers): array
    {
        foreach ($layers as &$layer) {
            foreach ($layer as $idx_y => $row) {
                foreach ($row as $idx_x => $cell) {
                    $command = new Command($cell);
                    $expansion = $command->getExpansion();

                    for ($iy = 0; $iy < $expansion['y']; $iy++) {
                        $layer[$idx_y + $iy][$idx_x] = $command->getCommand();

                        for ($ix = 0; $ix < $expansion['x']; $ix++) {
                            $layer[$idx_y + $iy][$idx_x + $ix]
                                = $command->getCommand();
                        }
                    }
                }
            }
        }
        unset($layer);

        return $layers;
    }

    /**
     * Parses a blueprint line into an array of commands.
     *
     * This method filters out any commands that are not allowed.
     *
     * @param string $line The blueprint line to parse.
     *
     * @return string[] An array of commands.
     */
    protected function parseLine(string $line): array
    {
        $line = trim($line);
        $csv_line = str_getcsv($line);

        $row = [];

        foreach ($csv_line as $idx => $cell) {
            $command = new Command($cell);

            if ($command->isAllowedCommand()) {
                $row[$idx] = $command->getFormatted();
            }
        }

        return $row;
    }
}
