<?php declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Tests\Destruction;

use DrdPlus\Destruction\BaseTimeOfDestruction;
use DrdPlus\Properties\Body\Size;
use DrdPlus\Tables\Measurements\Square\SquareBonus;
use DrdPlus\Tables\Measurements\Time\TimeBonus;
use DrdPlus\Tables\Measurements\Time\TimeTable;
use DrdPlus\Tables\Measurements\Volume\VolumeBonus;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerObject;
use Granam\Tests\Tools\TestWithMockery;

class BaseTimeOfDestructionTest extends TestWithMockery
{
    /**
     * @test
     */
    public function It_is_time_bonus(): void
    {
        $baseTimeOfDestruction = new BaseTimeOfDestruction(new IntegerObject(123), new TimeTable());
        self::assertInstanceOf(TimeBonus::class, $baseTimeOfDestruction);
    }

    /**
     * @test
     * @dataProvider provideValueAndExpectedTimeBOnus
     * @param int $value
     * @param int $expectedTimeBonus
     */
    public function I_can_create_it_for_an_item_size_and_body_size_and_volume_and_square(int $value, int $expectedTimeBonus): void
    {
        $directlyCreated = new BaseTimeOfDestruction(new IntegerObject($value), Tables::getIt()->getTimeTable());
        self::assertSame($expectedTimeBonus, $directlyCreated->getValue());

        $forItemSize = BaseTimeOfDestruction::createForItemSize(new IntegerObject($value), Tables::getIt()->getTimeTable());
        self::assertSame($expectedTimeBonus, $forItemSize->getValue());
        self::assertEquals($directlyCreated, $forItemSize);

        $forBodySize = BaseTimeOfDestruction::createForBodySize(Size::getIt($value), Tables::getIt()->getTimeTable());
        self::assertSame($expectedTimeBonus, $forBodySize->getValue());
        self::assertEquals($directlyCreated, $forBodySize);

        $forVolume = BaseTimeOfDestruction::createForItemOfVolume(
            new VolumeBonus($value, Tables::getIt()->getDistanceTable()),
            Tables::getIt()->getTimeTable()
        );
        self::assertSame($expectedTimeBonus, $forVolume->getValue());
        self::assertEquals($directlyCreated, $forVolume);

        $forSquare = BaseTimeOfDestruction::createForItemOfSquare(
            new SquareBonus($value, Tables::getIt()->getDistanceTable()),
            Tables::getIt()->getTimeTable()
        );
        self::assertSame($expectedTimeBonus, $forSquare->getValue());
        self::assertEquals($directlyCreated, $forSquare);
    }

    public function provideValueAndExpectedTimeBOnus(): array
    {
        return [
            [123, 174],
            [-51, 0],
            [999, 1050],
        ];
    }
}