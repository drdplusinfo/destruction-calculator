<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var Controller $controller */
?>
<div class="panel">
    <h4>Něco tlustšího</h4>
    <div class="example">
        tlustá zeď, bytelné dveře, pořádný led..
    </div>
    <label>
        Objem ničeného předmětu či jeho části
        <input type="number" value="<?= number_format($controller->getSelectedVolumeValue(), 2); ?>"
               name="<?= $controller::VOLUME_VALUE ?>">
    </label>
    <div class="example">
        například díra 30 cm x 30 cm v ledu tlustém 25 cm = 30x30x25 = 22500 cm<span class="upper-index">3</span> =
        22.5 litru
    </div>
    <label>
        Jednotka objemu
        <select name="<?= $controller::VOLUME_UNIT ?>">
            <?php foreach ($controller->getVolumeUnits() as $volumeUnit) { ?>
                <option value="<?= $volumeUnit->getValue() ?>"
                        <?php if ($controller->getSelectedVolumeUnit()->getValue() === $volumeUnit->getValue()) { ?>selected="selected"<?php } ?>><?= $volumeUnit->translateTo('cs') ?></option>
            <?php } ?>
        </select>
    </label>
    <?php $fatigueFromVoluminousItemDestruction = $controller->getFatigueFromVoluminousItemDestruction();
    if (!$fatigueFromVoluminousItemDestruction) { ?>
        <div class="error">Únava <strong>mimo rozsah</strong></div>
    <?php } else { ?>
        <div>Únava <strong><?= $fatigueFromVoluminousItemDestruction->getValue() ?></strong></div>
    <?php } ?>
    <div class="panel">
        Doba ničení <span class="keyword">něčeho tlustšího</span>
        <?php $realTimeOfVoluminousItemDestruction = $controller->getTimeOfVoluminousItemDestruction();
        if (!$realTimeOfVoluminousItemDestruction) { ?>
            <div class="error">Doba ničení mimo rozsah</div>
        <?php } else { ?>
            <strong><?= $realTimeOfVoluminousItemDestruction->getValue() . ' ' . TimeUnitCode::getIt($realTimeOfVoluminousItemDestruction->getUnit())->translateTo('cs', $realTimeOfVoluminousItemDestruction->getValue()) ?></strong>
        <?php } ?>
    </div>
</div>