<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\ItemHoldingCode;
use DrdPlus\Codes\Units\VolumeUnitCode;
use DrdPlus\Destruction\BaseTimeOfDestruction;
use DrdPlus\Destruction\Destruction;
use DrdPlus\Destruction\MaterialResistance;
use DrdPlus\Destruction\RealTimeOfDestruction;
use DrdPlus\Destruction\RollOnDestruction;
use DrdPlus\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Body\Size;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Tables\Measurements\Volume\Volume;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerObject;

class Controller extends \DrdPlus\Configurator\Skeleton\Controller
{
    /** @var Destruction */
    private $destruction;
    /** @var Tables */
    private $tables;

    public function __construct(Tables $tables)
    {
        parent::__construct('destruction' /* cookies postfix */);
        $this->destruction = new Destruction($tables);
        $this->tables = $tables;
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
     * @param MaterialCode $materialCode
     * @return \DrdPlus\Destruction\MaterialResistance
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    public function getMaterialResistance(MaterialCode $materialCode): MaterialResistance
    {
        return $this->destruction->getMaterialResistance($materialCode);
    }

    /**
     * @return array|VolumeUnitCode[]
     */
    public function getVolumeUnits(): array
    {
        return \array_map(
            function (string $volumeUnitValue) {
                return VolumeUnitCode::getIt($volumeUnitValue);
            },
            VolumeUnitCode::getPossibleValues()
        );
    }

    public function destructItem(
        string $materialValue,
        int $rollOnDestructingValue,
        string $meleeWeaponlikeValue,
        bool $weaponIsInappropriate,
        int $strengthValue,
        string $weaponHoldingValue,
        int $itemSizeValue
    ): RealTimeOfDestruction
    {
        $rollOnDestruction = $this->getRollOnDestruction(
            $materialValue,
            $rollOnDestructingValue,
            $meleeWeaponlikeValue,
            $weaponIsInappropriate,
            $strengthValue,
            $weaponHoldingValue
        );
        $itemSize = new IntegerObject($itemSizeValue);

        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemSize($itemSize, $this->tables->getTimeTable()),
            $rollOnDestruction,
            $this->tables
        );
    }

    private function getRollOnDestruction(
        string $materialValue,
        int $rollOnDestructingValue,
        string $meleeWeaponlikeValue,
        bool $weaponIsInappropriate,
        int $strengthValue,
        string $weaponHoldingValue
    ): RollOnDestruction
    {
        $materialCode = MaterialCode::getIt($materialValue);
        $meleeWeaponlikeCode = $this->tables->getArmourer()->getMeleeWeaponlikeCode($meleeWeaponlikeValue);
        $strength = Strength::getIt($strengthValue);
        $itemHoldingCode = ItemHoldingCode::getIt($weaponHoldingValue);
        $powerOfDestruction = $this->destruction->getPowerOfDestruction(
            $meleeWeaponlikeCode,
            $strength,
            $itemHoldingCode,
            $weaponIsInappropriate
        );
        $rollOnDestructing = new RollOnQuality(0, Roller2d6DrdPlus::getIt()->generateRoll($rollOnDestructingValue));

        return $this->destruction->getRollOnDestruction(
            $powerOfDestruction,
            $this->getMaterialResistance($materialCode),
            $rollOnDestructing
        );
    }

    public function destructStatueLike(
        string $materialValue,
        int $rollOnDestructingValue,
        string $meleeWeaponlikeValue,
        bool $weaponIsInappropriate,
        int $strengthValue,
        string $weaponHoldingValue,
        int $bodySizeValue
    ): RealTimeOfDestruction
    {
        $rollOnDestruction = $this->getRollOnDestruction(
            $materialValue,
            $rollOnDestructingValue,
            $meleeWeaponlikeValue,
            $weaponIsInappropriate,
            $strengthValue,
            $weaponHoldingValue
        );
        $bodySize = Size::getIt($bodySizeValue);

        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForBodySize($bodySize, $this->tables->getTimeTable()),
            $rollOnDestruction,
            $this->tables
        );
    }

    public function destructVoluminousItem(
        string $materialValue,
        int $rollOnDestructingValue,
        string $meleeWeaponlikeValue,
        bool $weaponIsInappropriate,
        int $strengthValue,
        string $weaponHoldingValue,
        float $volumeValue,
        string $volumeUnitValue
    ): RealTimeOfDestruction
    {
        $rollOnDestruction = $this->getRollOnDestruction(
            $materialValue,
            $rollOnDestructingValue,
            $meleeWeaponlikeValue,
            $weaponIsInappropriate,
            $strengthValue,
            $weaponHoldingValue
        );
        $volume = new Volume($volumeValue, $volumeUnitValue, $this->tables->getVolumeTable());

        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemOfVolume($volume->getBonus(), $this->tables->getTimeTable()),
            $rollOnDestruction,
            $this->tables
        );
    }
}