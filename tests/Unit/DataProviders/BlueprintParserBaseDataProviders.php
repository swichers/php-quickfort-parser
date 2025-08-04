<?php declare(strict_types=1);

namespace QuickFort\tests\Unit\DataProviders;

class BlueprintParserBaseDataProviders
{
    public static function dataProviderConstructorOptions(): array
    {
        return [
            'null text gives empty array' => [null, []],
            'dig text gives dig array' => ["#dig\nd,#", [[['d']]]],
        ];
    }
    public static function dataProviderLinesAsHeader():array {
        return [
            'command with no space after hash' => [
                '#dig',
                [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => null,
                ],
            ],
            'command with space after hash' => [
                '# dig',
                [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => null,
                ],
            ],
            'command with comment' => [
                '#dig Stairs leading down to a small room below',
                [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => 'Stairs leading down to a small room below',
                ],
            ],
            'command with start, spaces, and comment' => [
                '#dig start(3; 3; Center tile of a 5-tile square) Regular blueprint comment',
                [
                    'command' => 'dig',
                    'start'   => [
                        'x'       => 3,
                        'y'       => 3,
                        'comment' => 'Center tile of a 5-tile square',
                    ],
                    'comment' => 'Regular blueprint comment',
                ],
            ],
            'command with start, no spaces, and comment' => [
                '#dig start(3;3;Center tile of a 5-tile square) Regular blueprint comment',
                [
                    'command' => 'dig',
                    'start'   => [
                        'x'       => 3,
                        'y'       => 3,
                        'comment' => 'Center tile of a 5-tile square',
                    ],
                    'comment' => 'Regular blueprint comment',
                ],
            ],
            'commands with embedded command does nothing' => [
                '#dig the same area with d(3x3) specified in row 1, col 1',
                [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => 'the same area with d(3x3) specified in row 1, col 1',
                ],
            ],
            'trailing commas do not break anything' => [
                '#dig Simple bedroom example.,,,',
                [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => 'Simple bedroom example.',
                ],
            ],
            'empty command does nothing' => [
                '',
                [
                    'command' => null,
                    'start'   => null,
                    'comment' => null,
                ],
            ],
        ];
    }

}
