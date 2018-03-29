<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Armaments\MeleeWeaponCode;
use DrdPlus\Codes\Armaments\MeleeWeaponlikeCode;
use DrdPlus\Codes\Armaments\ShieldCode;
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
use Granam\Integer\IntegerInterface;
use Granam\Integer\IntegerObject;

class Controller extends \DrdPlus\Configurator\Skeleton\Controller
{

    public const VOLUME_UNIT = 'volume_unit';
    public const VOLUME_VALUE = 'volume_value';
    public const MATERIAL = 'material';
    public const ROLL_ON_DESTRUCTING = 'roll_on_destructing';
    public const SELECTED_MELEE_WEAPONLIKE = 'selected_melee_weaponlike';
    public const WEAPON_IS_INAPPROPRIATE = 'weapon_is_inappropriate';
    public const STRENGTH = 'strength';
    public const WEAPON_HOLDING_VALUE = 'weapon_holding_value';
    public const ITEM_SIZE = 'item_size';
    public const BODY_SIZE = 'body_size';

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

    public function getSelectedVolumeUnit(): VolumeUnitCode
    {
        $volumeUnitValue = $this->getHistory()->getValue(self::VOLUME_UNIT);
        if ($volumeUnitValue) {
            return VolumeUnitCode::getIt($volumeUnitValue);
        }
        $possibleValues = VolumeUnitCode::getPossibleValues();
        $defaultUnitValue = \reset($possibleValues);

        return VolumeUnitCode::getIt($defaultUnitValue);
    }

    /**
     * @return float
     * @throws \DrdPlus\Calculators\Destruction\Exceptions\UnknownVolumeUnit
     */
    public function getSelectedVolumeValue(): float
    {
        $selectedVolumeUnit = $this->getSelectedVolumeUnit();
        switch ($selectedVolumeUnit->getValue()) {
            case VolumeUnitCode::LITER :
                $minimal = \max(10.0/** minimal liters for @see VolumeTable */, $this->getHistory()->getValue(self::VOLUME_VALUE) ?? 0.0);

                return \min(1000/** maximal liters for @see VolumeTable */, $minimal);
            case VolumeUnitCode::CUBIC_METER :
                $minimal = \max(0.01/** minimal cubic meters for @see VolumeTable */, $this->getHistory()->getValue(self::VOLUME_VALUE) ?? 0.0);

                return \min(1000/** maximal cubic meters for @see VolumeTable */, $minimal);
            case VolumeUnitCode::CUBIC_KILOMETER :
                $minimal = \max(0.001/** minimal cubic kilometers for @see VolumeTable */, $this->getHistory()->getValue(self::VOLUME_VALUE) ?? 0.0);

                return \min(0.9/** maximal cubic meters for @see VolumeTable */, $minimal);
            default :
                throw new Exceptions\UnknownVolumeUnit('Unknown volume unit ' . $selectedVolumeUnit);
        }
    }

    public function getSelectedMaterial(): MaterialCode
    {
        $materialValue = $this->getHistory()->getValue(self::MATERIAL);
        if ($materialValue) {
            return MaterialCode::getIt($materialValue);
        }
        $possibleValues = MaterialCode::getPossibleValues();
        $defaultMaterialValue = \reset($possibleValues);

        return MaterialCode::getIt($defaultMaterialValue);
    }

    /**
     * @return MeleeWeaponlikeCode
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     */
    public function getSelectedMeleeWeaponlike(): MeleeWeaponlikeCode
    {
        $meleeWeaponlikeValue = $this->getHistory()->getValue(self::SELECTED_MELEE_WEAPONLIKE);
        if ($meleeWeaponlikeValue) {
            return $this->tables->getArmourer()->getMeleeWeaponlikeCode($meleeWeaponlikeValue);
        }
        $possibleValues = MeleeWeaponCode::getPossibleValues();
        $possibleValues = \array_unique(\array_merge($possibleValues, ShieldCode::getPossibleValues()));
        $defaultMeleeWeaponlikeValue = \reset($possibleValues);

        return $this->tables->getArmourer()->getMeleeWeaponlikeCode($defaultMeleeWeaponlikeValue);
    }

    /**
     * @return IntegerInterface
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function getSelectedRollOnDestructing(): IntegerInterface
    {
        return new IntegerObject($this->getHistory()->getValue(self::ROLL_ON_DESTRUCTING) ?? 6);
    }

    public function getSelectedWeaponIsInappropriate(): bool
    {
        // false by default
        return (bool)$this->getHistory()->getValue(self::WEAPON_IS_INAPPROPRIATE);
    }

    /**
     * @return Strength
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function getSelectedStrength(): Strength
    {
        return Strength::getIt($this->getHistory()->getValue(self::STRENGTH) ?? 0);
    }

    /**
     * @return IntegerInterface
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function getSelectedItemSize(): IntegerInterface
    {
        return new IntegerObject($this->getHistory()->getValue(self::ITEM_SIZE) ?? 0);
    }

    public function getSelectedBodySize(): Size
    {
        return Size::getIt($this->getHistory()->getValue(self::BODY_SIZE) ?? 0);
    }

    public function getSelectedWeaponHolding(): ItemHoldingCode
    {
        $weaponHoldingValue = $this->getHistory()->getValue(self::WEAPON_HOLDING_VALUE);
        if ($weaponHoldingValue) {
            return ItemHoldingCode::getIt($weaponHoldingValue);
        }
        $possibleValues = ItemHoldingCode::getPossibleValues();
        $defaultItemHoldingValue = \reset($possibleValues);

        return ItemHoldingCode::getIt($defaultItemHoldingValue);
    }

    /**
     * @return RealTimeOfDestruction
     * @throws \DrdPlus\Calculators\Destruction\Exceptions\UnknownVolumeUnit
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    public function getRealTimeOfVoluminousItemDestruction(): RealTimeOfDestruction
    {
        $volume = new Volume($this->getSelectedVolumeValue(), $this->getSelectedVolumeUnit(), $this->tables->getVolumeTable());

        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemOfVolume($volume->getBonus(), $this->tables->getTimeTable()),
            $this->getRollOnDestruction(),
            $this->tables
        );
    }

    /**
     * @return RealTimeOfDestruction
     * @throws \DrdPlus\Calculators\Destruction\Exceptions\UnknownVolumeUnit
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    public function getRealTimeOfBasicItemDestruction(): RealTimeOfDestruction
    {
        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemSize($this->getSelectedItemSize(), $this->tables->getTimeTable()),
            $this->getRollOnDestruction(),
            $this->tables
        );
    }

    /**
     * @return RollOnDestruction
     * @throws \DrdPlus\Calculators\Destruction\Exceptions\UnknownVolumeUnit
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    private function getRollOnDestruction(): RollOnDestruction
    {
        $powerOfDestruction = $this->destruction->getPowerOfDestruction(
            $this->getSelectedMeleeWeaponlike(),
            $this->getSelectedStrength(),
            $this->getSelectedWeaponHolding(),
            $this->getSelectedWeaponIsInappropriate()
        );
        $rollOnDestructing = new RollOnQuality(
            0 /* no preconditions */,
            Roller2d6DrdPlus::getIt()->generateRoll($this->getSelectedRollOnDestructing())
        );

        return $this->destruction->getRollOnDestruction(
            $powerOfDestruction,
            $this->getMaterialResistance($this->getSelectedMaterial()),
            $rollOnDestructing
        );
    }

    /**
     * @return RealTimeOfDestruction
     * @throws \DrdPlus\Calculators\Destruction\Exceptions\UnknownVolumeUnit
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     * @throws \DrdPlus\Tables\Environments\Exceptions\UnknownMaterialToGetResistanceFor
     */
    public function getRealTimeOfStatueLikeDestruction(): RealTimeOfDestruction
    {
        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForBodySize($this->getSelectedBodySize(), $this->tables->getTimeTable()),
            $this->getRollOnDestruction(),
            $this->tables
        );
    }
}