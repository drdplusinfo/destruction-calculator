<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator;

use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\Units\SquareUnitCode;
use DrdPlus\Codes\Units\TimeUnitCode;
use DrdPlus\Codes\Units\VolumeUnitCode;
use DrdPlus\Destruction\BaseTimeOfDestruction;
use DrdPlus\Destruction\Destruction;
use DrdPlus\Destruction\MaterialResistance;
use DrdPlus\Destruction\PowerOfDestruction;
use DrdPlus\Destruction\RealTimeOfDestruction;
use DrdPlus\Destruction\RollOnDestruction;
use DrdPlus\RollsOn\QualityAndSuccess\RollOnQuality;
use DrdPlus\Tables\Measurements\Fatigue\Fatigue;
use DrdPlus\Tables\Measurements\Partials\Exceptions\RequestedDataOutOfTableRange;
use DrdPlus\Tables\Measurements\Partials\Exceptions\UnknownBonus;
use DrdPlus\Tables\Measurements\Square\Square;
use DrdPlus\Tables\Measurements\Time\Exceptions\CanNotConvertThatBonusToTime;
use DrdPlus\Tables\Measurements\Time\Time;
use DrdPlus\Tables\Measurements\Volume\Volume;
use DrdPlus\Tables\Tables;
use Granam\DiceRolls\Templates\Rollers\Roller2d6DrdPlus;
use Granam\DiceRolls\Templates\Rolls\Roll2d6DrdPlus;
use Granam\Integer\IntegerInterface;
use Granam\Integer\IntegerObject;
use Granam\Strict\Object\StrictObject;

class CurrentDestruction extends StrictObject
{
    /**
     * @var CurrentValues
     */
    private $currentValues;
    /**
     * @var Destruction
     */
    private $destruction;
    /**
     * @var Tables
     */
    private $tables;
    /**
     * @var CurrentArmaments
     */
    private $currentArmaments;
    /**
     * @var CurrentProperties
     */
    private $currentProperties;
    /**
     * @var Roll2d6DrdPlus
     */
    private $currentRollOnDestructing;
    /**
     * @var Roll2d6DrdPlus|null
     */
    private $robotRollOnDestructing;

    public function __construct(
        CurrentArmaments $currentArmaments,
        CurrentProperties $currentProperties,
        CurrentValues $currentValues,
        Destruction $destruction,
        Tables $tables
    )
    {
        $this->currentValues = $currentValues;
        $this->destruction = $destruction;
        $this->tables = $tables;
        $this->currentArmaments = $currentArmaments;
        $this->currentProperties = $currentProperties;
    }

    public function getCurrentMaterial(): MaterialCode
    {
        $materialValue = $this->currentValues->getCurrentValue(DestructionRequest::MATERIAL);
        return MaterialCode::findIt($materialValue ?? '');
    }

    public function getCurrentRollOnDestruction(): RollOnDestruction
    {
        return $this->destruction->getRollOnDestruction(
            $this->getCurrentPowerOfDestruction(),
            $this->getMaterialResistance($this->getCurrentMaterial()),
            $this->getCurrentRollOnDestructionQuality()
        );
    }

    public function getCurrentRollOnDestructing(): IntegerInterface
    {
        return $this->getRobotRollOnDestructing()
            ?? new IntegerObject(
                $this->currentValues->getCurrentValue(DestructionRequest::ROLL_ON_DESTRUCTING)
                ?? 6
            );
    }

    private function getRobotRollOnDestructing(): ?Roll2d6DrdPlus
    {
        if ($this->robotRollOnDestructing === null && $this->shouldRollOnDestructing()) {
            $this->robotRollOnDestructing = Roller2d6DrdPlus::getIt()->roll();
        }
        return $this->robotRollOnDestructing;
    }

    public function getCurrentMaterialResistance(): MaterialResistance
    {
        return $this->getMaterialResistance($this->getCurrentMaterial());
    }

    public function getMaterialResistance(MaterialCode $materialCode): MaterialResistance
    {
        return $this->destruction->getMaterialResistance($materialCode);
    }

    private function getCurrentRealTimeOfStatueLikeDestruction(): RealTimeOfDestruction
    {
        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForBodySize($this->currentProperties->getCurrentBodySize(), $this->tables->getTimeTable()),
            $this->getCurrentRollOnDestruction(),
            $this->tables
        );
    }

    public function getCurrentFatigueFromStatueLikeDestruction(): ?Fatigue
    {
        try {
            return $this->getCurrentRealTimeOfStatueLikeDestruction()->getFatigue();
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        } catch (RequestedDataOutOfTableRange $canNotConvertThatBonusToTime) {
            return null;
        }
    }

    public function getCurrentTimeOfStatueLikeDestruction(): ?Time
    {
        return $this->getTimeOfDestruction($this->getCurrentRealTimeOfStatueLikeDestruction());
    }

    private function getTimeOfDestruction(RealTimeOfDestruction $realTimeOfDestruction): ?Time
    {
        $time = $realTimeOfDestruction->findTime(TimeUnitCode::HOUR);
        if (!$time) {
            try {
                $time = $realTimeOfDestruction->findTime();
            } catch (UnknownBonus $unknownBonus) {
                return null;
            }
        }
        if (!$time) {
            return null;
        }
        if ($time->getValue() > 1) {
            return $time;
        }
        $timeWithLesserUnit = $time->findInLesserUnit();
        while ($timeWithLesserUnit !== null && $timeWithLesserUnit->getValue() < 1) {
            $timeWithLesserUnit = $timeWithLesserUnit->findInLesserUnit();
        }
        return $timeWithLesserUnit ?? $time;
    }

    public function getCurrentRollOnDestructionQuality(): RollOnQuality
    {
        if ($this->currentRollOnDestructing === null) {
            $this->currentRollOnDestructing = new RollOnQuality(
                0 /* no preconditions */,
                Roller2d6DrdPlus::getIt()->generateRoll($this->getCurrentRollOnDestructing())
            );
        }
        return $this->currentRollOnDestructing;
    }

    public function getCurrentPowerOfDestruction(): PowerOfDestruction
    {
        return $this->destruction->getPowerOfDestruction(
            $this->currentArmaments->getCurrentMeleeWeapon(),
            $this->currentProperties->getCurrentStrength(),
            $this->currentArmaments->getCurrentMeleeWeaponHolding(),
            $this->isCurrentToolInappropriate()
        );
    }

    public function isCurrentToolInappropriate(): bool
    {
        if ($this->isCurrentWeaponInapropriateTool()) {
            return true;
        }
        // false by default
        return (bool)$this->currentValues->getCurrentValue(DestructionRequest::INAPPROPRIATE_TOOL);
    }

    public function isCurrentWeaponInapropriateTool(): bool
    {
        return $this->currentArmaments->getCurrentMeleeWeapon()->isUnarmed();
    }

    public function getCurrentTimeOfBasicItemDestruction(): ?Time
    {
        return $this->getTimeOfDestruction($this->getCurrentRealTimeOfBasicItemDestruction());
    }

    private function getCurrentRealTimeOfBasicItemDestruction(): RealTimeOfDestruction
    {
        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemSize($this->currentProperties->getCurrentItemSize(), $this->tables->getTimeTable()),
            $this->getCurrentRollOnDestruction(),
            $this->tables
        );
    }

    public function getCurrentBasicItemDestructionFatigue(): ?Fatigue
    {
        try {
            return $this->getCurrentRealTimeOfBasicItemDestruction()->getFatigue();
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        } catch (RequestedDataOutOfTableRange $canNotConvertThatBonusToTime) {
            return null;
        }
    }

    public function getCurrentTimeOfSquareItemDestruction(): ?Time
    {
        try {
            return $this->getTimeOfDestruction($this->getCurrentRealTimeOfSquareItemDestruction());
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        } catch (RequestedDataOutOfTableRange $canNotConvertThatBonusToTime) {
            return null;
        }
    }

    private function getCurrentRealTimeOfSquareItemDestruction(): RealTimeOfDestruction
    {
        $square = new Square($this->getCurrentSquareValue(), $this->getCurrentSquareUnit()->getValue(), $this->tables->getDistanceTable());

        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemOfSquare($square->getBonus(), $this->tables->getTimeTable()),
            $this->getCurrentRollOnDestruction(),
            $this->tables
        );
    }

    public function getCurrentFatigueFromSquareItemDestruction(): ?Fatigue
    {
        try {
            return $this->getCurrentRealTimeOfSquareItemDestruction()->getFatigue();
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        } catch (RequestedDataOutOfTableRange $canNotConvertThatBonusToTime) {
            return null;
        }
    }

    private function getCurrentRealTimeOfVoluminousItemDestruction(): RealTimeOfDestruction
    {
        $volume = new Volume($this->getCurrentVolumeValue(), $this->getCurrentVolumeUnit()->getValue(), $this->tables->getDistanceTable());

        return new RealTimeOfDestruction(
            BaseTimeOfDestruction::createForItemOfVolume($volume->getBonus(), $this->tables->getTimeTable()),
            $this->getCurrentRollOnDestruction(),
            $this->tables
        );
    }

    public function getCurrentVolumeValue(): float
    {
        $selectedVolumeUnit = $this->getCurrentVolumeUnit();
        switch ($selectedVolumeUnit->getValue()) {
            case VolumeUnitCode::LITER :
                $minimal = max(10.0/** minimal liters for @see VolumeTable */, $this->currentValues->getCurrentValue(DestructionRequest::VOLUME_VALUE) ?? 10.0);

                return (float)min(1000/** maximal liters for @see VolumeTable */, $minimal);
            case VolumeUnitCode::CUBIC_METER :
                $minimal = max(0.01/** minimal cubic meters for @see VolumeTable */, $this->currentValues->getCurrentValue(DestructionRequest::VOLUME_VALUE) ?? 1.0);

                return (float)min(1000/** maximal cubic meters for @see VolumeTable */, $minimal);
            case VolumeUnitCode::CUBIC_KILOMETER :
                $minimal = max(0.001/** minimal cubic kilometers for @see VolumeTable */, $this->currentValues->getCurrentValue(DestructionRequest::VOLUME_VALUE) ?? 0.1);

                return (float)min(0.9/** maximal cubic meters for @see VolumeTable */, $minimal);
            default :
                throw new Exceptions\UnknownVolumeUnit('Unknown volume unit ' . $selectedVolumeUnit);
        }
    }

    public function getCurrentVolumeUnit(): VolumeUnitCode
    {
        $volumeUnitValue = $this->currentValues->getCurrentValue(DestructionRequest::VOLUME_UNIT);
        if ($volumeUnitValue) {
            return VolumeUnitCode::getIt($volumeUnitValue);
        }
        return VolumeUnitCode::getIt(VolumeUnitCode::CUBIC_METER);
    }

    public function getCurrentSquareUnit(): SquareUnitCode
    {
        $squareUnitValue = $this->currentValues->getCurrentValue(DestructionRequest::SQUARE_UNIT);
        if ($squareUnitValue) {
            return SquareUnitCode::getIt($squareUnitValue);
        }
        $possibleValues = SquareUnitCode::getPossibleValues();
        $defaultUnitValue = reset($possibleValues);

        return SquareUnitCode::getIt($defaultUnitValue);
    }

    public function getCurrentSquareValue(): float
    {
        $selectedSquareUnit = $this->getCurrentSquareUnit();
        switch ($selectedSquareUnit->getValue()) {
            case SquareUnitCode::SQUARE_DECIMETER :
                $minimal = max(10.0/** minimal liters for @see SquareTable */, $this->currentValues->getCurrentValue(DestructionRequest::SQUARE_VALUE) ?? 0.0);

                return min(1000/** maximal liters for @see SquareTable */, $minimal);
            case SquareUnitCode::SQUARE_METER :
                $minimal = max(0.01/** minimal cubic meters for @see SquareTable */, $this->currentValues->getCurrentValue(DestructionRequest::SQUARE_VALUE) ?? 0.0);

                return min(1000/** maximal cubic meters for @see SquareTable */, $minimal);
            case SquareUnitCode::SQUARE_KILOMETER :
                $minimal = max(0.001/** minimal cubic kilometers for @see SquareTable */, $this->currentValues->getCurrentValue(DestructionRequest::SQUARE_VALUE) ?? 0.0);

                return min(0.9/** maximal cubic meters for @see SquareTable */, $minimal);
            default :
                throw new Exceptions\UnknownSquareUnit('Unknown volume unit ' . $selectedSquareUnit);
        }
    }

    public function getCurrentFatigueFromVoluminousItemDestruction(): ?Fatigue
    {
        try {
            return $this->getCurrentRealTimeOfVoluminousItemDestruction()->getFatigue();
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        } catch (RequestedDataOutOfTableRange $canNotConvertThatBonusToTime) {
            return null;
        }
    }

    public function getCurrentTimeOfVoluminousItemDestruction(): ?Time
    {
        try {
            return $this->getTimeOfDestruction($this->getCurrentRealTimeOfVoluminousItemDestruction());
        } catch (UnknownBonus $unknownBonus) {
            return null;
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            return null;
        } catch (RequestedDataOutOfTableRange $canNotConvertThatBonusToTime) {
            return null;
        }
    }

    public function shouldRollOnDestructing(): bool
    {
        return (bool)$this->currentValues->getCurrentValue(DestructionRequest::SHOULD_ROLL_ON_DESTRUCTING);
    }

    public function getCurrent2d6RollTitle(): string
    {
        $robotRollOnDestructing = $this->getCurrentRollOnDestructionQuality()->getRoll();
        if ($robotRollOnDestructing) {
            return implode(', ', $robotRollOnDestructing->getRolledNumbers()) . ' = ' . $robotRollOnDestructing->getValue();
        }
        return '(ruční hod) ' . $this->getCurrentRollOnDestructionQuality()->getValue();
    }
}