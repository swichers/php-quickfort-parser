<?php declare(strict_types=1);

namespace QuickFort\tests\Unit\DataProviders;

class HeaderValidation
{
    public static function validDigHeaders(): array {
        return [
            ["#dig\n"],
            ["#dig"],
            ["# dig\n"],
            ["# dig \n"],
            ["# dig, \n"],
            ["#dig the same area with d(3x3) specified in row 1, col 1\n"],
            ["#dig Stairs leading down to a small room below\n"],
            ["#dig start(3; 3; Center tile of a 5-tile square) Regular blueprint comment\n"],
        ];
    }

    public static function invalidDigHeaders(): array {
        return [
            ['#build'],
            ["#build\n"],
            [''],
            ["\n"],
        ];
    }
}
