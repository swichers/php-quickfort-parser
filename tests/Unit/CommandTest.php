<?php declare(strict_types=1);

namespace QuickFort\tests\Unit;

use PHPUnit\Framework\TestCase;
use QuickFort\Parser\Command;

/**
 * Class CommandTest.
 */
class CommandTest extends TestCase
{

    /**
     * Validate we properly parse a layer up command.
     *
     * @return void
     */
    public function testIsLayerUp(): void
    {
        $command = new Command('#<');

        $this->assertTrue($command->isLayerUp());
        $this->assertFalse($command->isLayerDown());
    }

    /**
     * Validate we properly parse a layer down command.
     *
     * @return void
     */
    public function testIsLayerDown(): void
    {
        $command = new Command('#>');

        $this->assertTrue($command->isLayerDown());
        $this->assertFalse($command->isLayerUp());
    }

    /**
     * Validate that we detect allowed and non-allowed commands.
     *
     * @return void
     */
    public function testIsAllowedCommand(): void
    {
        $command = new Command('d');
        $this->assertTrue($command->isAllowedCommand());

        $command = new Command('m');
        $this->assertFalse($command->isAllowedCommand());
    }

    /**
     * Validate that we detect commands that do nothing.
     *
     * @return void
     */
    public function testIsNoOp(): void
    {
        $not_noops = range('a', 'z');
        foreach ($not_noops as $not_noop) {
            $command = new Command($not_noop);
            $this->assertFalse($command->isNoOp());
        }

        $noops = '#~`';
        foreach (str_split($noops) as $noop) {
            $command = new Command($noop);
            $this->assertTrue($command->isNoOp());
        }
    }

    /**
     * Validate we can get a formatted command back.
     *
     * @return void
     */
    public function testGetFormatted(): void
    {
        $command = new Command('d(3x3');
        $this->assertEquals('d(3x3)', $command->getFormatted());

        $command = new Command('d');
        $this->assertEquals('d', $command->getFormatted());
    }

    /**
     * Validate we can detect command expansion.
     *
     * @return void
     */
    public function testHasExpansion(): void
    {
        $command = new Command('d(3x3');
        $this->assertTrue($command->hasExpansion());

        $command = new Command('d');
        $this->assertFalse($command->hasExpansion());
    }

    /**
     * Validate we can parse command expansions.
     *
     * @return void
     */
    public function testGetExpansion(): void
    {
        $command = new Command('d');
        $this->assertEquals(['x' => 1, 'y' => 1], $command->getExpansion());

        $command = new Command('d(3x3)');
        $this->assertEquals(['x' => 3, 'y' => 3], $command->getExpansion());

        $command = new Command('d(1x3)');
        $this->assertEquals(['x' => 1, 'y' => 3], $command->getExpansion());
    }

    /**
     * Validate that we can get the correct command.
     *
     * @return void
     */
    public function testGetCommand(): void
    {
        $command = new Command('d');
        $this->assertEquals('d', $command->getCommand());

        $command = new Command('d(3x3)');
        $this->assertEquals('d', $command->getCommand());
    }

    /**
     * Validate we can convert the command to a string.
     *
     * @return void
     */
    public function testGetString(): void
    {
        $command = new Command('d');
        $this->assertEquals('d', (string)$command);

        $command = new Command('d(3x3)');
        $this->assertEquals('d', (string)$command);
    }

    /**
     * Validate we can detect comments.
     *
     * @return void
     */
    public function testIsComment(): void
    {
        $command = new Command('#');
        $this->assertTrue($command->isComment());

        $command = new Command('d');
        $this->assertFalse($command->isComment());
    }
}
