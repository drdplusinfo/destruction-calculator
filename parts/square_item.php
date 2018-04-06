<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var Controller $controller */
?>
<div class="panel">
    <h4>Něco plošného</h4>
    <div class="example">
        lehké dveře, tenký led..
    </div>
    <label>
        Plocha ničeného předmětu či jeho části
        <input type="number" value="<?= number_format($controller->getSelectedSquareValue(), 2); ?>"
               name="<?= $controller::SQUARE_VALUE ?>">
    </label>
    <div class="example">
        například otvor 1/2 na 1/2 metru v prkenné podlaze = 0.5x0.5 = 0.25 m<span class="upper-index">2</span>
    </div>
    <label>
        Jednotka plochy
        <select name="<?= $controller::SQUARE_UNIT ?>">
            <?php foreach ($controller->getSquareUnits() as $squareUnit) { ?>
                <option value="<?= $squareUnit->getValue() ?>"
                        <?php if ($controller->getSelectedSquareUnit()->getValue() === $squareUnit->getValue()) { ?>selected="selected"<?php } ?>><?= $squareUnit->translateTo('cs') ?></option>
            <?php } ?>
        </select>
    </label>
    <?php $fatigueFromSquareItemDestruction = $controller->getFatigueFromSquareItemDestruction();
    if (!$fatigueFromSquareItemDestruction) { ?>
        <div class="error">Únava <strong>mimo rozsah</strong></div>
    <?php } else { ?>
        <div>Únava <strong><?= $fatigueFromSquareItemDestruction->getValue() ?></strong></div>
    <?php } ?>
    <div class="panel">
        Doba ničení <span class="keyword">něčeho plošného</span>
        <?php $timeOfSquareItemDestruction = $controller->getTimeOfSquareItemDestruction();
        if (!$timeOfSquareItemDestruction) { ?>
            <div class="error">Doba ničení <strong>mimo rozsah</strong></div>
        <?php } else { ?>
            <strong><?= $timeOfSquareItemDestruction->getValue() . ' ' . TimeUnitCode::getIt($timeOfSquareItemDestruction->getUnit())->translateTo('cs', $timeOfSquareItemDestruction->getValue()) ?></strong>
        <?php } ?>
    </div>
</div>