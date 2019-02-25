<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Destruction;

use DrdPlus\Tables\Measurements\Distance\Distance;
use DrdPlus\Tables\Measurements\Fatigue\Fatigue;
use DrdPlus\Tables\Measurements\Time\TimeBonus;
use DrdPlus\Tables\Measurements\Volume\Volume;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerObject;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class RealTimeOfDestructionTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $realTimeOfDestruction = new RealTimeOfDestruction(
            $this->createBaseTimeOfDestruction(123),
            $this->createRollOnDestruction(24),
            Tables::getIt()
        );
        self::assertSame(99 /* 123 - 24 */, $realTimeOfDestruction->getValue());
        self::assertInstanceOf(
            TimeBonus::class,
            $realTimeOfDestruction,
            RealTimeOfDestruction::class . ' should be usable as a ' . TimeBonus::class
        );
        self::assertEquals(new Fatigue(14000 /* bonus 99 */, Tables::getIt()->getFatigueTable()), $realTimeOfDestruction->getFatigue());
    }

    /**
     * @param int $value
     * @return BaseTimeOfDestruction|MockInterface
     */
    private function createBaseTimeOfDestruction(int $value): BaseTimeOfDestruction
    {
        $baseTimeOfDestruction = $this->mockery(BaseTimeOfDestruction::class);
        $baseTimeOfDestruction->shouldReceive('getValue')
            ->andReturn($value);

        return $baseTimeOfDestruction;
    }

    /**
     * @param int $value
     * @return RollOnDestruction|MockInterface
     */
    private function createRollOnDestruction(int $value): RollOnDestruction
    {
        $rollOnDestruction = $this->mockery(RollOnDestruction::class);
        $rollOnDestruction->shouldReceive('getValue')
            ->andReturn($value);

        return $rollOnDestruction;
    }

    /**
     * @link https://pph.drdplus.info/#priklad_skutecne_doby_rozbijeni_steny
     * @test
     */
    public function Kroll_magnus_destroys_wall_in_expected_time(): void
    {
        $volume = new Volume(5 * 3 * 0.2, Volume::CUBIC_METER, Tables::getIt()->getDistanceTable());
        self::assertSame(3.0, $volume->getValue());
        $volumeBonus = $volume->getBonus();
        self::assertSame(9, $volumeBonus->getValue());
        $baseTimeOfDestructionByVolume = BaseTimeOfDestruction::createForItemOfVolume(
            $volumeBonus,
            Tables::getIt()->getTimeTable()
        );
        self::assertSame(60, $baseTimeOfDestructionByVolume->getValue());
        $realTimeOfDestructionByVolume = new RealTimeOfDestruction(
            $baseTimeOfDestructionByVolume,
            $this->createRollOnDestruction(30 /* strength, luck */ - 18 /* material */),
            Tables::getIt()
        );
        self::assertSame(48, $realTimeOfDestructionByVolume->getValue());
        $realTimeOfDestructionByVolume->getFatigue();

        $a = new Distance(5, Distance::METER, Tables::getIt()->getDistanceTable());
        $b = new Distance(3, Distance::METER, Tables::getIt()->getDistanceTable());
        $c = new Distance(0.2, Distance::METER, Tables::getIt()->getDistanceTable());
        $baseTimeOfDestructionByDistanceBonuses = BaseTimeOfDestruction::createForItemSize(
            new IntegerObject($a->getBonus()->getValue() + $b->getBonus()->getValue() + $c->getBonus()->getValue()),
            Tables::getIt()->getTimeTable()
        );
        self::assertSame(59, $baseTimeOfDestructionByDistanceBonuses->getValue());
        $realTimeOfDestructionByDistanceBonuses = new RealTimeOfDestruction(
            $baseTimeOfDestructionByDistanceBonuses,
            $this->createRollOnDestruction(30 /* strength, luck */ - 18 /* material */),
            Tables::getIt()
        );
        self::assertSame(47, $realTimeOfDestructionByDistanceBonuses->getValue());
    }
}