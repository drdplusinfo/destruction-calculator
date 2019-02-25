<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Tests\Destruction;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Destruction\Destruction;
use DrdPlus\Destruction\PowerOfDestruction;
use DrdPlus\Destruction\MaterialResistance;
use DrdPlus\Destruction\RollOnDestruction;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Tables\Environments\MaterialResistancesTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class DestructionTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_get_power_of_destruction(): void
    {
        $hand = MeleeWeaponCode::getIt(MeleeWeaponCode::HAND);
        $strength = Strength::getIt(123);
        $itemHoldingCode = ItemHoldingCode::getIt(ItemHoldingCode::MAIN_HAND);
        $armourer = $this->mockery(Armourer::class);
        $armourer->shouldReceive('getPowerOfDestruction')
            ->once()
            ->with($hand, $strength, $itemHoldingCode, false)
            ->andReturn(456);
        $armourer->shouldReceive('getTables')
            ->andReturn(Tables::getIt());
        /** @var Armourer $armourer */
        $destruction = new Destruction($armourer);
        $powerOfDestruction = $destruction->getPowerOfDestruction($hand, $strength, $itemHoldingCode, false);
        self::assertSame(456, $powerOfDestruction->getValue());
    }

    /**
     * @test
     */
    public function I_can_get_material_resistance(): void
    {
        $bronze = MaterialCode::getIt(MaterialCode::BRONZE);
        $armourer = $this->mockery(Armourer::class);
        $materialResistancesTable = $this->mockery(MaterialResistancesTable::class);
        $materialResistancesTable->shouldReceive('getResistanceOfMaterial')
            ->once()
            ->with($bronze)
            ->andReturn(777);
        /** @var MaterialResistancesTable $materialResistancesTable */
        $armourer->shouldReceive('getTables')
            ->andReturn($this->createTablesWithMaterialResistancesTable($materialResistancesTable));
        /** @var Armourer $armourer */
        $destruction = new Destruction($armourer);
        $materialResistance = $destruction->getMaterialResistance($bronze);
        self::assertSame(777, $materialResistance->getValue());
    }

    /**
     * @param MaterialResistancesTable $materialResistancesTable
     * @return Tables|MockInterface
     */
    private function createTablesWithMaterialResistancesTable(MaterialResistancesTable $materialResistancesTable): Tables
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMaterialResistancesTable')
            ->atLeast()->once()
            ->andReturn($materialResistancesTable);

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_get_roll_on_destruction(): void
    {
        $destruction = new Destruction(Armourer::getIt());
        $rollOnDestruction = $destruction->getRollOnDestruction(
            $powerOfDestruction = $this->createPowerOfDestruction(123),
            $materialResistance = $this->createMaterialResistance(234),
            $rollOnQuality = $this->createRollOnQuality()
        );

        self::assertEquals(
            new RollOnDestruction($powerOfDestruction, $materialResistance, $rollOnQuality),
            $rollOnDestruction
        );
    }

    /**
     * @param int $value
     * @return MockInterface|PowerOfDestruction
     */
    private function createPowerOfDestruction(int $value): PowerOfDestruction
    {
        $powerOfDestruction = $this->mockery(PowerOfDestruction::class);
        $powerOfDestruction->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn($value);

        return $powerOfDestruction;
    }

    /**
     * @param int $value
     * @return MockInterface|MaterialResistance
     */
    private function createMaterialResistance(int $value): MaterialResistance
    {
        $materialResistance = $this->mockery(MaterialResistance::class);
        $materialResistance->shouldReceive('getValue')
            ->atLeast()->once()
            ->andReturn($value);

        return $materialResistance;
    }

    /**
     * @return MockInterface|RollOnQuality
     */
    private function createRollOnQuality(): RollOnQuality
    {
        return $this->mockery(RollOnQuality::class);
    }
}