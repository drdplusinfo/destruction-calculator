<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator;

use DrdPlus\CalculatorSkeleton\CurrentValues;
use DrdPlus\Properties\Body\Size;
use Granam\Integer\IntegerInterface;
use Granam\Integer\IntegerObject;

class CurrentProperties extends \DrdPlus\AttackSkeleton\CurrentProperties
{
    /**
     * @var CurrentValues
     */
    private $currentValues;

    public function __construct(CurrentValues $currentValues)
    {
        parent::__construct($currentValues);
        $this->currentValues = $currentValues;
    }

    public function getCurrentItemSize(): IntegerInterface
    {
        return new IntegerObject($this->currentValues->getCurrentValue(DestructionRequest::ITEM_SIZE) ?: 0);
    }

    public function getCurrentBodySize(): Size
    {
        return Size::getIt($this->currentValues->getCurrentValue(DestructionRequest::BODY_SIZE) ?: 0);
    }

}
