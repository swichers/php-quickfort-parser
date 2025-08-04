<?php declare(strict_types=1);

namespace QuickFort\tests\Unit;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use QuickFort\Parser\BlueprintParserBase;
use QuickFort\tests\Unit\DataProviders\BlueprintParserBaseDataProviders;
use QuickFort\tests\Unit\DataProviders\BlueprintsToValues;

/**
 * Class BlueprintParserBaseTest.
 */
class BlueprintParserBaseTest extends TestCase
{
    public function testConstructorCanAcceptNothing(): void
    {
        $parser = new BlueprintParserBase();
        $this->assertEquals([], $parser->getLayers());
    }

    /**
     * Tests that our constructor can initialize a blueprint.
     *
     * @return void
     */
    #[DataProviderExternal(BlueprintParserBaseDataProviders::class, 'dataProviderConstructorOptions')]
    public function testConstructorHandlesBlueprintText(?string $blueprintText, array $expectedBlueprintValues): void {
        $parser = new BlueprintParserBase($blueprintText);
        $this->assertEquals($expectedBlueprintValues, $parser->getLayers());
    }

    /**
     * Validates we can properly parse a blueprint header line.
     *
     * @return void
     */
    #[DataProviderExternal(BlueprintParserBaseDataProviders::class, 'dataProviderLinesAsHeader')]
    public function testParseLineAsHeader(?string $blueprintText, array $headerData): void {
        $parser = new BlueprintParserBase();
        $parser->setBlueprint($blueprintText);
        $this->assertEquals($headerData, $parser->getHeader());
    }

    /**
     * Validate that we properly handle parsing different types of blueprints.
     *
     * @return void
     */
    #[DataProviderExternal(BlueprintsToValues::class, 'simpleLines')]
    #[DataProviderExternal(BlueprintsToValues::class, 'layeredLines')]
    #[DataProviderExternal(BlueprintsToValues::class, 'commandExpansion')]
    public function testBlueprintLayerParsing(array $blueprintLines, array $expectedBlueprintValues): void {
        $parser = new BlueprintParserBase();
        $parser->setBlueprint(implode(PHP_EOL, $blueprintLines));
        $this->assertEquals($expectedBlueprintValues, $parser->getLayers());
    }

    /**
     * Validate we can get the original blueprint back.
     *
     * @return void
     */
    #[DataProviderExternal(BlueprintsToValues::class, 'simpleLines')]
    #[DataProviderExternal(BlueprintsToValues::class, 'layeredLines')]
    #[DataProviderExternal(BlueprintsToValues::class, 'commandExpansion')]
    public function testGetBlueprint(array $blueprintLines, ?array $noop = null): void
    {
        $parser = new BlueprintParserBase();

        $blueprint = implode(PHP_EOL, $blueprintLines);
        $parser->setBlueprint($blueprint);
        $this->assertEquals($blueprint, $parser->getBlueprint());
    }
}
