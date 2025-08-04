<?php declare(strict_types=1);

namespace QuickFort\tests\Unit;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use QuickFort\Parser\Command;
use QuickFort\tests\Unit\DataProviders\CommandDataProviders;

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
    #[DataProviderExternal(CommandDataProviders::class, 'layerShifting')]
    public function testIsLayerUpOrDown(string $commandString, bool $isLayerUp): void {
        $command = new Command($commandString);
        $this->assertEquals($isLayerUp, $command->isLayerUp());
        $this->assertEquals(!$isLayerUp, $command->isLayerDown());
    }

    /**
     * Validate that we detect allowed and non-allowed commands.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'allowedCommands')]
    public function testIsAllowedCommand(string $commandString): void
    {
        $command = new Command($commandString);
        $this->assertTrue($command->isAllowedCommand());
    }

    #[DataProviderExternal(CommandDataProviders::class, 'disallowedCommands')]
    public function testIsDisallowedCommand(string $commandString): void
    {
        $command = new Command($commandString);
        $this->assertFalse($command->isAllowedCommand());
    }

    /**
     * Validate that we detect commands that do nothing.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'noopValidity')]
    public function testIsNoOp(string $commandString, bool $isNoop): void
    {
        $command = new Command($commandString);
        $this->assertEquals($isNoop, $command->isNoOp());
    }

    /**
     * Validate we can get a formatted command back.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'complexCommandWithBase')]
    public function testGetFormatted(string $commandString, ?string $baseCommand = null): void {
        $command = new Command($commandString);
        $this->assertEquals($commandString, $command->getFormatted());
    }

    /**
     * Validate we can detect command expansion.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'commandHasExpansion')]
    public function testHasExpansion(string $commandString, bool $hasExpansion): void {
        $command = new Command($commandString);
        $this->assertEquals($hasExpansion, $command->hasExpansion());
    }

    /**
     * Validate we can parse command expansions.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'commandWithExpansion')]
    public function testGetExpansion(string $commandString, array $expectedExpansion): void {
        $command = new Command($commandString);
        $this->assertEquals($expectedExpansion, $command->getExpansion());
    }

    /**
     * Validate that we can get the correct command.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'complexCommandWithBase')]
    public function testGetCommand(string $commandString, string $baseCommand): void {
        $command = new Command($commandString);
        $this->assertEquals($baseCommand, $command->getCommand());
    }

    /**
     * Validate we can convert the command to a string.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'complexCommandWithBase')]
    public function testGetString(string $commandString, string $baseCommand): void {
        $command = new Command($commandString);
        $this->assertEquals($baseCommand, (string) $command);
    }

    /**
     * Validate we can detect comments.
     *
     * @return void
     */
    #[DataProviderExternal(CommandDataProviders::class, 'commentCommand')]
    public function testIsComment(string $commandString, bool $isComment): void
    {
        $command = new Command($commandString);
        $this->assertEquals($isComment, $command->isComment());
    }
}
