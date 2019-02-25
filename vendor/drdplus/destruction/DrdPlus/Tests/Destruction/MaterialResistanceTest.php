<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Tests\Destruction;

use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Destruction\MaterialResistance;
use DrdPlus\Tables\Environments\MaterialResistancesTable;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Tests\Tools\TestWithMockery;

class MaterialResistanceTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it()
    {
        $materialResistance = new MaterialResistance(123);
        self::assertSame(123, $materialResistance->getValue());
        self::assertInstanceOf(IntegerInterface::class, $materialResistance);
    }

    /**
     * @test
     */
    public function I_can_let_create_it_for_specific_material()
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMaterialResistancesTable')
            ->once()
            ->andReturn($materialResistancesTable = $this->mockery(MaterialResistancesTable::class));
        $materialResistancesTable->shouldReceive('getResistanceOfMaterial')
            ->once()
            ->with($bakedCay = MaterialCode::getIt(MaterialCode::BAKED_CAY))
            ->andReturn(321);
        /** @var Tables $tables */
        $forBakedCay = MaterialResistance::createForMaterial($bakedCay, $tables);
        self::assertEquals(new MaterialResistance(321), $forBakedCay);
    }
}