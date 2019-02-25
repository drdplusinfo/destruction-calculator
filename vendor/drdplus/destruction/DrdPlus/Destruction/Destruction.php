<?php
namespace DrdPlus\Destruction;

use DrdPlus\Armourer\Armourer;
use DrdPlus\Codes\Armaments\MeleeWeaponlikeCode;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\BaseProperties\Strength;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use Granam\Strict\Object\StrictObject;

/**
 * @link https://pph.drdplus.info/#niceni
 */
class Destruction extends StrictObject
{
    /** @var Armourer */
    private $armourer;

    public function __construct(Armourer $armourer)
    {
        $this->armourer = $armourer;
    }

    /**
     * There is NO malus for missing strength (we are not fighting, just smashing)
     *
     * @link https://pph.drdplus.info/#vypocet_sily_niceni
     * @param MeleeWeaponlikeCode $meleeWeaponlikeCode
     * @param Strength $strength
     * @param ItemHoldingCode $itemHoldingCode
     * @param bool $weaponIsInappropriate
     * @return PowerOfDestruction it is a bonus of destruction strength, not the final value
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     */
    public function getPowerOfDestruction(
        MeleeWeaponlikeCode $meleeWeaponlikeCode,
        Strength $strength,
        ItemHoldingCode $itemHoldingCode,
        bool $weaponIsInappropriate
    ): PowerOfDestruction
    {
        return new PowerOfDestruction($meleeWeaponlikeCode, $strength, $itemHoldingCode, $weaponIsInappropriate, $this->armourer);
    }

    /**
     * @param MaterialCode $materialCode
     * @return MaterialResistance
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    public function getMaterialResistance(MaterialCode $materialCode): MaterialResistance
    {
        return new MaterialResistance(
            $this->armourer->getTables()->getMaterialResistancesTable()->getResistanceOfMaterial($materialCode)
        );
    }

    public function getRollOnDestruction(
        PowerOfDestruction $powerOfDestruction,
        MaterialResistance $materialResistance,
        RollOnQuality $rollOnDestructing
    ): RollOnDestruction
    {
        return new RollOnDestruction($powerOfDestruction, $materialResistance, $rollOnDestructing);
    }
}