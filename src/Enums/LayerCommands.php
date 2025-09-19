<?php declare(strict_types=1);

namespace QuickFort\Enums;

/**
 * Enum for layer commands.
 *
 * @package QuickFort\Enums
 */
enum LayerCommands: string
{
    /**
     * Go up one layer.
     */
    case UP = '#<';
    /**
     * Go down one layer.
     */
    case DOWN = '#>';
}
