<?php declare(strict_types=1);

namespace QuickFort\tests\Unit;

use PHPUnit\Framework\TestCase;
use QuickFort\Parser\BlueprintParserBase;

/**
 * Class BlueprintParserBaseTest.
 */
class BlueprintParserBaseTest extends TestCase
{

    /**
     * Tests that our constructor can initialize a blueprint.
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $parser = new BlueprintParserBase();
        $this->assertEquals([], $parser->getLayers());

        $parser = new BlueprintParserBase(null);
        $this->assertEquals([], $parser->getLayers());

        $parser = new BlueprintParserBase("#dig\nd,#");
        $this->assertEquals([[['d']]], $parser->getLayers());
    }

    /**
     * Validates we can properly parse a blueprint header line.
     *
     * @return void
     */
    public function testParseLineAsHeader(): void
    {
        $testing_values = [
            [
                'start'  => '#dig',
                'expect' => [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => null,
                ],
            ],
            [
                'start'  => '# dig',
                'expect' => [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => null,
                ],
            ],
            [
                'start'  => '#dig Stairs leading down to a small room below',
                'expect' => [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => 'Stairs leading down to a small room below',
                ],
            ],
            [
                'start'  => '#dig start(3; 3; Center tile of a 5-tile square) Regular blueprint comment',
                'expect' => [
                    'command' => 'dig',
                    'start'   => [
                        'x'       => 3,
                        'y'       => 3,
                        'comment' => 'Center tile of a 5-tile square',
                    ],
                    'comment' => 'Regular blueprint comment',
                ],
            ],
            [
                'start'  => '#dig start(3;3;Center tile of a 5-tile square) Regular blueprint comment',
                'expect' => [
                    'command' => 'dig',
                    'start'   => [
                        'x'       => 3,
                        'y'       => 3,
                        'comment' => 'Center tile of a 5-tile square',
                    ],
                    'comment' => 'Regular blueprint comment',
                ],
            ],
            [
                'start'  => '#dig the same area with d(3x3) specified in row 1, col 1',
                'expect' => [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => 'the same area with d(3x3) specified in row 1, col 1',
                ],
            ],
            [
                'start'  => '#dig Simple bedroom example.,,,',
                'expect' => [
                    'command' => 'dig',
                    'start'   => null,
                    'comment' => 'Simple bedroom example.',
                ],
            ],
            [
                'start'  => '',
                'expect' => [
                    'command' => null,
                    'start'   => null,
                    'comment' => null,
                ],
            ],
        ];

        $parser = new BlueprintParserBase();

        foreach ($testing_values as $header_data) {
            $parser->setBlueprint($header_data['start']);
            $this->assertEquals($header_data['expect'], $parser->getHeader());
        }
    }

    /**
     * Validate we can get a may layer from simple designations.
     *
     * @return void
     */
    public function testSimpleProcessLines(): void
    {
        $parser = new BlueprintParserBase();

        $blueprint = [
            '#dig',
            'd,d,d,#',
            'd,~,i,#',
            'j,`,d,#',
            '#,#,#,#',
        ];

        $expected = [
            [
                ['d', 'd', 'd'],
                [0 => 'd', 2 => 'i'],
                [0 => 'j', 2 => 'd'],
                [],
            ],
        ];

        $parser->setBlueprint(implode(PHP_EOL, $blueprint));
        $this->assertEquals($expected, $parser->getLayers());

        $blueprint = [
            '#dig',
            'd,d,d,#',
            '~,~,~,#',
            'd,d,d,d,#',
            '#,#,#,#',
        ];

        $expected = [
            [
                ['d', 'd', 'd'],
                [],
                ['d', 'd', 'd', 'd'],
                [],
            ],
        ];

        $parser->setBlueprint(implode(PHP_EOL, $blueprint));
        $this->assertEquals($expected, $parser->getLayers());
    }

    /**
     * Validate that layers get reordered when building a map.
     *
     * @return void
     */
    public function testLayeredProcessLines(): void
    {
        $blueprint = [
            '# dig',
            '`,`,`,#',
            'j,`,j,#',
            '`,`,`,#',
            '#>,#,#,#',
            'u,d,d,#',
            'd,d,d,#',
            'd,d,d,#',
            '#<,#,#,#',
            'j,d,j,#',
            'j,d,j,#',
            'd,j,d,#',
            '#,#,#,#',
        ];

        $expected = [
            [
                [],
                [0 => 'j', 2 => 'j'],
                [],
            ],
            [
                [],
                ['j', 'd', 'j'],
                ['j', 'd', 'j'],
                ['d', 'j', 'd'],
                [],
            ],
            [
                [],
                ['u', 'd', 'd'],
                ['d', 'd', 'd'],
                ['d', 'd', 'd'],
            ],
        ];

        $parser = new BlueprintParserBase();
        $parser->setBlueprint(implode(PHP_EOL, $blueprint));
        $this->assertEquals($expected, $parser->getLayers());

        $blueprint = [
            '#dig Stairs leading down to a small room below',
            'j,`,`,#',
            '`,`,`,#',
            '`,`,`,#',
            '#>,#,#,#',
            'u,d,d,#',
            'd,d,d,#',
            'd,d,d,#',
            '#,#,#,#',
        ];

        $expected = [
            [
                ['j'],
                [],
                [],
            ],
            [
                [],
                ['u', 'd', 'd'],
                ['d', 'd', 'd'],
                ['d', 'd', 'd'],
                [],
            ],
        ];

        $parser->setBlueprint(implode(PHP_EOL, $blueprint));
        $this->assertEquals($expected, $parser->getLayers());

        $blueprint = [
            '#dig Stairs leading down to a small room below',
            'j,`,`,#',
            '`,`,`,#',
            '`,`,`,#',
            '#<,#,#,#',
            'd,j,i,#',
            'd,d,d,#',
            'd,d,d,#',
            '#,#,#,#',
            '#<,#,#,#',
            'u,u,u,#',
            'd,d,d,#',
            'd,d,d,#',
            '#,#,#,#',
        ];

        $expected = [
            [
                [],
                ['d', 'j', 'i'],
                ['d', 'd', 'd'],
                ['d', 'd', 'd'],
                [],
            ],
            [
                [],
                ['u', 'u', 'u'],
                ['d', 'd', 'd'],
                ['d', 'd', 'd'],
                [],
            ],
            [
                ['j'],
                [],
                [],
            ],
        ];

        $parser->setBlueprint(implode(PHP_EOL, $blueprint));
        $this->assertEquals($expected, $parser->getLayers());
    }

    /**
     * Validate that we properly handle command expansion.
     *
     * @return void
     */
    public function testExpandProcessLines(): void
    {
        $parser = new BlueprintParserBase();

        $blueprint = [
            '#dig',
            'd(3x3),#',
            '~,~,~,#',
            '`,`,`,#',
            '#,#,#,#',
        ];

        $expected = [
            [
                ['d', 'd', 'd'],
                ['d', 'd', 'd'],
                ['d', 'd', 'd'],
                [],
            ],
        ];

        $parser->setBlueprint(implode(PHP_EOL, $blueprint));
        $this->assertEquals($expected, $parser->getLayers());
    }

    /**
     * Validate we can get the original blueprint back.
     *
     * @return void
     */
    public function testGetBlueprint(): void
    {
        $parser = new BlueprintParserBase();

        $blueprint = implode(PHP_EOL, [
            '#dig',
            'd(3x3),#',
            '~,~,~,#',
            '`,`,`,#',
            '#,#,#,#',
        ]);

        $parser->setBlueprint($blueprint);
        $this->assertEquals($blueprint, $parser->getBlueprint());
    }
}
