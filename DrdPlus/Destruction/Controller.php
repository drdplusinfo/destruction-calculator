<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Armaments\MeleeWeaponlikeCode;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Destruction\Destruction;
use DrdPlus\Destruction\MaterialResistance;
use DrdPlus\Destruction\PowerOfDestruction;
use DrdPlus\Destruction\RollOnDestruction;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Tables\Tables;

class Controller extends \DrdPlus\Configurator\Skeleton\Controller
{
    /**
     * @var Destruction
     */
    private $destruction;

    public function __construct()
    {
        parent::__construct('destruction' /* cookies postfix */);
        $this->destruction = new Destruction(Tables::getIt());
    }

    /**
     * @return array|MaterialCode[]
     */
    public function getMaterialCodes(): array
    {
        return \array_map(
            function (string $materialValue) {
                return MaterialCode::getIt($materialValue);
            },
            MaterialCode::getPossibleValues()
        );
    }

    /**
     * @param string $material
     * @return \DrdPlus\Destruction\MaterialResistance
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    public function getMaterialResistance(string $material): MaterialResistance
    {
        $materialCode = MaterialCode::getIt($material);

        return $this->destruction->getMaterialResistance($materialCode);
    }

    /**
     * @param MeleeWeaponlikeCode $meleeWeaponlikeCode
     * @param Strength $strength
     * @param ItemHoldingCode $itemHoldingCode
     * @param bool $weaponIsInappropriate
     * @return PowerOfDestruction
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     */
    private function getPowerOfDestruction(
        MeleeWeaponlikeCode $meleeWeaponlikeCode,
        Strength $strength,
        ItemHoldingCode $itemHoldingCode,
        bool $weaponIsInappropriate
    ): PowerOfDestruction
    {
        return $this->destruction->getPowerOfDestruction(
            $meleeWeaponlikeCode,
            $strength,
            $itemHoldingCode,
            $weaponIsInappropriate
        );
    }

    private function getRollOnDestruction(
        PowerOfDestruction $powerOfDestruction,
        MaterialResistance $materialResistance,
        RollOnQuality $rollOnDestruction
    ): RollOnDestruction
    {
        return $this->destruction->getRollOnDestruction(
            $powerOfDestruction,
            $materialResistance,
            $rollOnDestruction
        );
    }
}