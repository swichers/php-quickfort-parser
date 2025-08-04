<?php declare(strict_types=1);

namespace QuickFort\tests\Unit\DataProviders;

use QuickFort\Enums\DigCommands;
use QuickFort\Enums\LayerCommands;

class CommandDataProviders
{
    public static function commandWithExpansion(): array
    {
        return [
            ['d', ['x' => 1, 'y' => 1]],
            ['d(3x3)', ['x' => 3, 'y' => 3]],
            ['d(1x3)', ['x' => 1, 'y' => 3]],
        ];
    }

    public static function commandHasExpansion():array {
        return [
            ['d(3x3', true],
            ['d(3x3)', true],
            ['d', false],
        ];
    }

    public static function allowedCommands(): array
    {
        $data = [];
        foreach (DigCommands::cases() as $case) {
            $data[] = [$case->value];
        }
        return $data;
    }

    public static function disallowedCommands(): array
    {
        return [
            ['m'],
        ];
    }

    public static function complexCommandWithBase():array {
        return [
            ['d',DigCommands::DIG->value],
            ['d(3x3)',DigCommands::DIG->value],
            ['d(1x3)',DigCommands::DIG->value],
        ];
    }

    public static function layerShifting():array {
        return [
            'layer shift up' => [LayerCommands::UP->value, true],
            'layer shift down' => [LayerCommands::DOWN->value, false],
        ];
    }

    public static function noopValidity():array {
        $data = [];
        foreach (self::validNoops() as $noop) {
            $data[$noop[0] . ' is valid'] = [$noop[0], true];
        }
        foreach (self::invalidNoops() as $noop) {
            $data[$noop[0] . ' is invalid'] = [$noop[0], false];
        }
        return $data;
    }

    public static function validNoops():array {
        return [
            ['#'],
            ['~'],
            ['`'],
        ];
    }

    public static function invalidNoops():array {
        return [
            ...range('a', 'z'),
        ];
    }

    public static function commentCommand(): array {
        return [
            ['#', true],
            ['d', false],
        ];
    }
}
