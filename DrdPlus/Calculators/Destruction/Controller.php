<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\Units\TimeUnitCode;
use DrdPlus\Codes\Units\VolumeUnitCode;
use DrdPlus\Destruction\BaseTimeOfDestruction;
use DrdPlus\Destruction\Destruction;
use DrdPlus\Destruction\MaterialResistance;
use DrdPlus\Destruction\PowerOfDestruction;
use DrdPlus\Destruction\RealTimeOfDestruction;
use DrdPlus\Destruction\RollOnDestruction;
use DrdPlus\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Body\Size;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Tables\Measurements\Fatigue\Fatigue;
use DrdPlus\Tables\Measurements\Time\Exceptions\CanNotConvertThatBonusToTime;
use DrdPlus\Tables\Measurements\Time\Time;
use DrdPlus\Tables\Measurements\Volume\Volume;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\IntegerObject;

class Controller extends \DrdPlus\Calculators\AttackSkeleton\Controller
{

    public const VOLUME_UNIT = 'volume_unit';
    public const VOLUME_VALUE = 'volume_value';
    public const MATERIAL = 'material';
    public const ROLL_ON_DESTRUCTING = 'roll_on_destructing';
    public const SHOULD_ROLL_ON_DESTRUCTING = 'new_roll_on_destructing';
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

    /**
     * @param Tables $tables
     * @throws \DrdPlus\Calculators\AttackSkeleton\Exceptions\BrokenNewArmamentValues
     */
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
     * @return IntegerInterface
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function getSelectedRollOnDestructing(): IntegerInterface
    {
        static $newRollOnDestructing = null;
        if ($newRollOnDestructing === null && $this->shouldRollOnDestructing()) {
            $newRollOnDestructing = Roller2d6DrdPlus::getIt()->roll()->getValue();
        }

        return new IntegerObject(
            $newRollOnDestructing ?? $this->getHistory()->getValue(self::ROLL_ON_DESTRUCTING) ?? 6
        );
    }

    /**
     * @return bool
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function shouldRollOnDestructing(): bool
    {
        return (bool)$this->getHistory()->getValue(self::SHOULD_ROLL_ON_DESTRUCTING);
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

    public function getTimeOfBasicItemDestruction(): ?Time
    {
        $realTimeOfBasicItemDestruction = $this->getRealTimeOfBasicItemDestruction();
        $inHours = $realTimeOfBasicItemDestruction->findTime(TimeUnitCode::HOUR);
        if ($inHours) {
            return $inHours;
        }

        return $realTimeOfBasicItemDestruction->findTime();
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
    private function getRealTimeOfBasicItemDestruction(): RealTimeOfDestruction
    {
        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemSize($this->getSelectedItemSize(), $this->tables->getTimeTable()),
            $this->getRollOnDestruction(),
            $this->tables
        );
    }

    public function getBasicItemDestructionFatigue(): ?Fatigue
    {
        try {
            return $this->getRealTimeOfBasicItemDestruction()->getFatigue();
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        }
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
    public function getRollOnDestruction(): RollOnDestruction
    {
        return $this->destruction->getRollOnDestruction(
            $this->getPowerOfDestruction(),
            $this->getMaterialResistance($this->getSelectedMaterial()),
            $this->getRollOnDestructing()
        );
    }

    /**
     * @return PowerOfDestruction
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotUseMeleeWeaponlikeBecauseOfMissingStrength
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownArmament
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\UnknownMeleeWeaponlike
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByTwoHands
     * @throws \DrdPlus\Tables\Armaments\Exceptions\CanNotHoldWeaponByOneHand
     */
    public function getPowerOfDestruction(): PowerOfDestruction
    {
        return $this->destruction->getPowerOfDestruction(
            $this->getAttack()->getCurrentMeleeWeapon(),
            $this->getCurrentProperties()->getCurrentStrength(),
            $this->getAttack()->getCurrentMeleeWeaponHolding(),
            $this->getSelectedWeaponIsInappropriate()
        );
    }

    /**
     * @return RollOnQuality
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public function getRollOnDestructing(): RollOnQuality
    {
        return new RollOnQuality(
            0 /* no preconditions */,
            Roller2d6DrdPlus::getIt()->generateRoll($this->getSelectedRollOnDestructing())
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