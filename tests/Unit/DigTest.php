<?php declare(strict_types=1);

namespace QuickFort\tests\Unit;

use PHPUnit\Framework\TestCase;
use QuickFort\Parser\Dig;

/**
 * Class DigTest.
 */
class DigTest extends TestCase
{

    /**
     * Validate we properly parse blueprint headers.
     *
     * @return void
     */
    public function testCheckHeader(): void
    {
        $headers = [
            "#dig\n",
            "#dig",
            "# dig\n",
            "# dig \n",
            "# dig, \n",
            "#dig the same area with d(3x3) specified in row 1, col 1\n",
            "#dig Stairs leading down to a small room below\n",
            "#dig start(3; 3; Center tile of a 5-tile square) Regular blueprint comment\n",
        ];
        $parser = new Dig();
        foreach ($headers as $header) {
            $parser->setBlueprint($header);
            $this->assertTrue($parser->checkHeader());
        }

        $parser->setBlueprint("#build");
        $this->assertFalse($parser->checkHeader());
    }
}
