<?php
declare(strict_types=1);

namespace DrdPlus\Destruction;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\MeleeWeaponlikeCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\BaseProperties\Strength;
use Granam\Integer\IntegerInterface;
use Granam\Strict\Object\StrictObject;

/**
 * @link https://pph.drdplus.info/#vypocet_sily_niceni
 * This is a bonus of destruction strength, not the final value.
 */
class PowerOfDestruction extends StrictObject implements IntegerInterface
{
    /** @var int */
    private $value;

    /**
     * @param MeleeWeaponlikeCode $meleeWeaponlikeCode
     * @param Strength $strength
     * @param ItemHoldingCode $itemHoldingCode
     * @param bool $weaponIsInappropriate
     * @param Armourer $armourer
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     */
    public function __construct(
        MeleeWeaponlikeCode $meleeWeaponlikeCode,
        Strength $strength,
        ItemHoldingCode $itemHoldingCode,
        bool $weaponIsInappropriate,
        Armourer $armourer
    )
    {
        $this->value = $armourer->getPowerOfDestruction(
            $meleeWeaponlikeCode,
            $strength,
            $itemHoldingCode,
            $weaponIsInappropriate
        );
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->getValue();
    }

}