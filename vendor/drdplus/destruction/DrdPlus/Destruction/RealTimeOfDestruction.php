<?php
declare(strict_types=1); // on PHP 7+ can be methods strict to scalar types of given parameters

namespace DrdPlus\Destruction;

use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Tables\Measurements\Fatigue\Fatigue;
use DrdPlus\Tables\Measurements\Time\Time;
use DrdPlus\Tables\Measurements\Time\TimeBonus;
use DrdPlus\Tables\Tables;

/**
 * @link https://pph.drdplus.info/#vypocet_skutecne_doby_niceni
 */
class RealTimeOfDestruction extends TimeBonus
{
    /** @var Tables */
    private $tables;

    public function __construct(
        BaseTimeOfDestruction $baseTimeOfDestruction,
        RollOnDestruction $rollOnDestruction,
        Tables $tables
    )
    {
        parent::__construct($baseTimeOfDestruction->getValue() - $rollOnDestruction->getValue(), $tables->getTimeTable());
        $this->tables = $tables;
    }

    /**
     * @return Fatigue
     * @throws \DrdPlus\Tables\Measurements\Time\Exceptions\CanNotConvertThatBonusToTime
     */
    public function getFatigue(): Fatigue
    {
        return new Fatigue(SumAndRound::round($this->getTime(Time::MINUTE)->getValue()), $this->tables->getFatigueTable());
    }
}