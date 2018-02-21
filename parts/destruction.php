<?php
namespace DrdPlus\Calculators\Destruction;

use \DrdPlus\Tables\Measurements\Time\Exceptions\CanNotConvertThatBonusToTime;

/** @var Controller $controller */
?>
    <label>Materiál
        <select name="material">
            <?php foreach ($controller->getMaterialCodes() as $materialCode) { ?>
                <option value="<?= $materialCode ?>"><?= $materialCode->getValue() ?>
                    (pevnost <?= $controller->getMaterialResistance($materialCode) ?>)
                </option>
            <?php } ?>
        </select>
    </label>

    <h4>Něco obyčejného</h4>
    <div class="example">
        běžný stůl, dlaždice, meč, lopata...
    </div>

    <h4>Tvarem připomínajícím tělo</h4>
    <div class="example">
        socha, krápník, rampouch, sloup...
    </div>

    <h4>Něco tlustšího</h4>
    <div class="example">
        zeď, bytelné dveře, led..
    </div>
    <label>
        Objem ničeného předmětu či jeho části
        <input type="number" value="<?= number_format($controller->getSelectedVolumeValue(), 2); ?>"
               name="<?= $controller::VOLUME_VALUE ?>">
    </label>
    <div class="example">
        díra 30 cm x 30 cm v ledu tlustém 25 cm = 30x30x25 = 22500 cm<span class="upper-index">3</span> = 22.5 litru
    </div>
    <label>
        Jednotka objemu
        <select name="<?= $controller::VOLUME_UNIT ?>">
            <?php foreach ($controller->getVolumeUnits() as $volumeUnit) { ?>
                <option value="<?= $volumeUnit->getValue() ?>"
                        <?php if ($controller->getSelectedVolumeUnit()->getValue() === $volumeUnit->getValue()) { ?>selected="selected"<?php } ?>><?= $volumeUnit->getValue() ?></option>
            <?php } ?>
        </select>
    </label>
    <?php $realTimeOfVoluminousItemDestruction = $controller->getRealTimeOfVoluminousItemDestruction();
$fatigueFromVoluminousItemDestruction = null;
try {
    $fatigueFromVoluminousItemDestruction = $realTimeOfVoluminousItemDestruction->getFatigue();
    ?>
    <div>
        Únava <?= $fatigueFromVoluminousItemDestruction->getValue() ?>
    </div>
    <?php
} catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
    ?>
    <div class="error">
        Únava mimo rozsah
    </div>
    <?php
}
