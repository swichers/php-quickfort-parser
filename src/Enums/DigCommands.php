<?php declare(strict_types=1);

namespace QuickFort\Enums;

enum DigCommands: string
{
    case DIG = 'd';
    case STAIR_DOWN = 'j';
    case STAIR_UP = 'u';
    case STAIR_UPDOWN = 'i';
    case CHANNEL = 'h';
    case RAMP = 'r';
    case REMOVE = 'x';
}
