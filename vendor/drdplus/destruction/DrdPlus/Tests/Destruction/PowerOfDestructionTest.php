<?php declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace DrdPlus\Tests\Destruction;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Destruction\PowerOfDestruction;
use DrdPlus\BaseProperties\Strength;
use Granam\Integer\IntegerInterface;
use Granam\Tests\Tools\TestWithMockery;
use Mockery\MockInterface;

class PowerOfDestructionTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_use_it(): void
    {
        $barbarianSword = MeleeWeaponCode::getIt(MeleeWeaponCode::BARBARIAN_SWORD);
        $strength = Strength::getIt(123);
        $itemHoldingCode = ItemHoldingCode::getIt(ItemHoldingCode::TWO_HANDS);
        $armourer = $this->mockery(Armourer::class);
        $armourer->shouldReceive('getPowerOfDestruction')
            ->once()
            ->with($barbarianSword, $strength, $itemHoldingCode, false)
            ->andReturn(456);
        /** @var Armourer $armourer */
        $powerOfDestruction = new PowerOfDestruction(
            $barbarianSword,
            $strength,
            $itemHoldingCode,
            false, /* weapon is appropriate */
            $armourer
        );
        self::assertSame(456, $powerOfDestruction->getValue());
        self::assertSame('456', (string)$powerOfDestruction);
        self::assertInstanceOf(
            IntegerInterface::class,
            $powerOfDestruction,
            PowerOfDestruction::class . ' should be usable as an ' . IntegerInterface::class
        );

        $armourer = $this->mockery(Armourer::class);
        /** @var MockInterface $armourer */
        $armourer->shouldReceive('getPowerOfDestruction')
            ->once()
            ->with($barbarianSword, $strength, $itemHoldingCode, true)
            ->andReturn(111);
        /** @var Armourer $armourer */
        $powerOfDestruction = new PowerOfDestruction(
            $barbarianSword,
            $strength,
            $itemHoldingCode,
            true, /* weapon is inappropriate */
            $armourer
        );
        self::assertSame(111, $powerOfDestruction->getValue());
    }
}