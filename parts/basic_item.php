<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var Controller $controller */
?>
<div class="panel">
    <h4>Něco obyčejného</h4>
    <div class="example">
        běžný stůl, dlaždice, meč, lopata...
    </div>
    <?php $fatigueFromBasicItemDestruction = $controller->getBasicItemDestructionFatigue();
    if (!$fatigueFromBasicItemDestruction) { ?>
        <div class="error">
            Únava <strong>mimo rozsah</strong>
        </div>
    <?php } else { ?>
        <label>Velikost věci <input type="number" value="<?= $controller->getSelectedItemSize() ?>"
                                    name="<?= $controller::ITEM_SIZE ?>"></label>
        <div>
            Únava <strong><?= $fatigueFromBasicItemDestruction->getValue() ?></strong>
        </div>
    <?php } ?>
    <div class="panel">
        Doba ničení <span class="keyword">něčeho obyčejného</span>
        <?php $realTimeOfBasicItemDestruction = $controller->getTimeOfBasicItemDestruction();
        if (!$realTimeOfBasicItemDestruction) { ?>
            <div class="error">
                Doba <strong>mimo rozsah</strong>
            </div>
        <?php } else { ?>
            <strong><?= $realTimeOfBasicItemDestruction->getValue() . ' ' . TimeUnitCode::getIt($realTimeOfBasicItemDestruction->getUnit())->translateTo('cs', $realTimeOfBasicItemDestruction->getValue()) ?></strong>
        <?php } ?>
    </div>
</div>