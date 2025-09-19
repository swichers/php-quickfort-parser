<?php declare(strict_types=1);

namespace QuickFort\Enums;

/**
 * Enum for dig commands.
 *
 * @package QuickFort\Enums
 */
enum DigCommands: string
{
    /**
     * Dig a tile.
     */
    case DIG = 'd';
    /**
     * Dig a downward staircase.
     */
    case STAIR_DOWN = 'j';
    /**
     * Dig an upward staircase.
     */
    case STAIR_UP = 'u';
    /**
     * Dig an up/down staircase.
     */
    case STAIR_UPDOWN = 'i';
    /**
     * Dig a channel.
     */
    case CHANNEL = 'h';
    /**
     * Dig a ramp.
     */
    case RAMP = 'r';
    /**
     * Remove a designation.
     */
    case REMOVE = 'x';
}
