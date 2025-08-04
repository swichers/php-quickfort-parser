<?php declare(strict_types=1);

namespace QuickFort\tests\Unit;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use QuickFort\Parser\Dig;
use QuickFort\tests\Unit\DataProviders\HeaderValidation;

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
    #[DataProviderExternal(HeaderValidation::class, 'validDigHeaders')]
    public function testCheckValidHeadersPass(string $header): void
    {
        $parser = new Dig();
        $parser->setBlueprint($header);
        $this->assertTrue($parser->checkHeader());
    }

    #[DataProviderExternal(HeaderValidation::class, 'invalidDigHeaders')]
    public function testCheckInvalidHeadersFail(?string $header): void
    {
        $parser = new Dig();
        $parser->setBlueprint($header);
        $this->assertFalse($parser->checkHeader());
    }
}
