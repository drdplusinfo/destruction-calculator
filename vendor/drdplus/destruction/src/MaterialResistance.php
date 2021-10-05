<?php declare(strict_types=1);

namespace DrdPlus\Destruction;

use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerObject;

class MaterialResistance extends IntegerObject
{
    public static function createForMaterial(MaterialCode $materialCode, Tables $tables): MaterialResistance
    {
        return new static($tables->getMaterialResistancesTable()->getResistanceOfMaterial($materialCode));
    }
}