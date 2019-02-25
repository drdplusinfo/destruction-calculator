<?php
declare(strict_types=1);

namespace DrdPlus\Destruction;

use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Square\SquareBonus;
use DrdPlus\Tables\Measurements\Time\TimeBonus;
use DrdPlus\Tables\Measurements\Time\TimeTable;
use DrdPlus\Tables\Measurements\Volume\VolumeBonus;
use Granam\Integer\IntegerInterface;

/**
 * This is an expression of a time consumed on the worst, but YET successful destruction of an item.
 */
class BaseTimeOfDestruction extends TimeBonus
{
    public function __construct(IntegerInterface $itemSize, TimeTable $timeTable)
    {
        parent::__construct($itemSize->getValue() + 51, $timeTable);
    }

    public static function createForItemSize(IntegerInterface $itemSize, TimeTable $timeTable)
    {
        return new static($itemSize, $timeTable);
    }

    public static function createForBodySize(Size $size, TimeTable $timeTable): BaseTimeOfDestruction
    {
        return new static($size, $timeTable);
    }

    public static function createForItemOfVolume(VolumeBonus $volumeBonus, TimeTable $timeTable): BaseTimeOfDestruction
    {
        return new static($volumeBonus, $timeTable);
    }

    public static function createForItemOfSquare(SquareBonus $squareBonus, TimeTable $timeTable): BaseTimeOfDestruction
    {
        return new static($squareBonus, $timeTable);
    }
}