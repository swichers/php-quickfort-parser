<?php declare(strict_types=1);

namespace QuickFort\Parser;

/**
 * Base QuickFort blueprint parser class.
 *
 * Provides a barebones implementation of QuickFort blueprint CSV parsing.
 */
class BlueprintParserBase implements BlueprintParserInterface
{

    /**
     * A key-value array of header information.
     *
     * @var array
     *
     * @see \QuickFort\Parser\BlueprintParserInterface::getHeader().
     */
    protected $blueprintHeader;

    /**
     * Original blueprint text.
     *
     * @var string
     */
    protected $originalBlueprint;

    /**
     * The lines of the blueprint, minus the header.
     *
     * @var string[]
     */
    protected $blueprintLines;

    /**
     * BlueprintParserBase constructor.
     *
     * @param string $blueprintText A blueprint to initialize with.
     */
    public function __construct(string $blueprintText = null)
    {
        if (!empty($blueprintText)) {
            $this->setBlueprint($blueprintText);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $blueprintText The new blueprint text to use.
     *
     * @return void
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
     * Parse a blueprint string into individual lines.
     *
     * @param string $text The blueprint to parse into individual lines.
     *
     * @return string[]
     *   The individual lines of the blueprint.
     */
    protected function textToLines(string $text): array
    {
        $lines = explode(PHP_EOL, $text) ?: [];
        $lines = array_map('trim', $lines);

        return $lines ?: [];
    }

    /**
     * Parse a blueprint line for header information.
     *
     * @param string $line The line to parse header information from.
     *
     * @return array
     *   An array of header information.
     *
     * @see \QuickFort\Parser\BlueprintParserInterface::getHeader().
     */
    protected function parseLineAsHeader(string $line): array
    {
        $header = [
            'command' => null,
            'start'   => null,
            'comment' => null,
        ];

        $line = trim($line);
        if (0 !== stripos($line, '#')) {
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
        if (0 === strpos($line, 'start(')) {
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
     *
     * @return string
     *   The blueprint text.
     */
    public function getBlueprint(): string
    {
        return $this->originalBlueprint;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     *   A key-value array of header information.
     */
    public function getHeader(): array
    {
        return $this->blueprintHeader;
    }

    /**
     * {@inheritdoc}
     *
     * @return array[]
     *   A nested array of processed blueprint layers.
     */
    public function getLayers(): array
    {
        return $this->processLines();
    }

    /**
     * Process blueprint lines into map layers.
     *
     * @return array[]
     *   A nested array of processed blueprint layers.
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
     * @param array $lines An array of blueprint lines.
     *
     * @return array[]
     *   An array of blueprint lines grouped into arrays for the layer they
     *   belong to.
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
     * @param array $layers A nested array of blueprint layers.
     *
     * @return array[]
     *   The original layers reordered by their layer up and down commands.
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
            } else {
                $adjusted[] = $layer;
            }
        }

        return $adjusted;
    }

    /**
     * Process the given layer lines into individual commands.
     *
     * @param array[] $layers An array of layers and their lines.
     *
     * @return array[]
     *   An array of layers and their individual commands.
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
     * Turns d(2x2) into:
     *   d,d
     *   d,d
     *
     * @param array[] $layers The layers to find area expansions within.
     *
     * @return array[]
     *   The layers with their area expansions replaced by individual commands.
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
     * Parse a blueprint line for available commands.
     *
     * @param string $line The blueprint line to parse commands from.
     *
     * @return string[]
     *   The parsed, filtered, and normalized commands.
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
