<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Units\TimeUnitCode;
use \DrdPlus\Tables\Measurements\Time\Exceptions\CanNotConvertThatBonusToTime;
use DrdPlus\Tables\Measurements\Time\Time;

/** @var Controller $controller */
?>
<hr>
<div class="block">
    <label>Materiál
        <select name="material">
            <?php foreach ($controller->getMaterialCodes() as $materialCode) { ?>
                <option value="<?= $materialCode ?>"
                        <?php if ($controller->getSelectedMaterial()->getValue() === $materialCode->getValue()) { ?>selected="selected"
                    <?php } ?>><?= $materialCode->translateTo('cs') ?>
                    (pevnost <?= $controller->getMaterialResistance($materialCode) ?>)
                </option>
            <?php } ?>
        </select>
    </label>
</div>
<div class="block">
    <h4>Poškození</h4>
    <label>Síla<input type="number" value="<?= $controller->getSelectedStrength() ?>" step="1"></label>
    <?php include __DIR__ . '/../vendor/drd-plus/attack-skeleton/parts/melee_weapon.php' ?>
    <div class="block">
        <div>
            <a target="_blank" href="https://pph.drdplus.info/#vypocet_sily_niceni">Síla ničení</a>
            <strong><?= $controller->getPowerOfDestruction()->getValue() ?></strong>
        </div>
        <div>
            <label>Hod na ničení 2k6<span class="upper-index">+</span>
                <input name="<?= $controller::ROLL_ON_DESTRUCTING ?>" type="number"
                       value="<?= $controller->getSelectedRollOnDestructing()->getValue() ?>">
            </label>
            <button type="submit" name="<?= $controller::SHOULD_ROLL_ON_DESTRUCTING ?>" value="1" class="manual">Hodit
                2k6<span class="upper-index">+</span>
            </button>
        </div>
        <div>
            Předmět poškozen <strong><?= $controller->getRollOnDestruction()->isSuccess() ? 'ano' : 'ne' ?></strong>
        </div>
    </div>
</div>
<?php if (!$controller->getRollOnDestruction()->isSuccess()) {
    return;
} ?>
<div class="block">
    <div class="panel">
        <h4>Něco obyčejného</h4>
        <div class="example">
            běžný stůl, dlaždice, meč, lopata...
        </div>
        <?php $realTimeOfBasicItemDestruction = $controller->getRealTimeOfBasicItemDestruction();
        $fatigueFromBasicItemDestruction = null;
        try {
            $fatigueFromBasicItemDestruction = $realTimeOfBasicItemDestruction->getFatigue();
            ?>
            <label>Velikost věci <input type="number" value="<?= $controller->getSelectedItemSize() ?>"
                                        name="<?= $controller::ITEM_SIZE ?>"></label>
            <div>
                Únava <strong><?= $fatigueFromBasicItemDestruction->getValue() ?></strong>
            </div>
            <?php
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            ?>
            <div class="error">
                Únava <strong>mimo rozsah</strong>
            </div>
        <?php } ?>
        <div class="panel">
            <h5>Doba ničení něčeho obyčejného</h5>
            <?php $realTimeOfBasicItemDestruction = $controller->getRealTimeOfBasicItemDestruction()->findTime(Time::HOUR);
            echo $realTimeOfBasicItemDestruction->getValue() . ' ' . TimeUnitCode::getIt($realTimeOfBasicItemDestruction->getUnit())->translateTo('cs', $realTimeOfBasicItemDestruction->getValue()) ?>
        </div>
    </div>

    <div class="panel">
        <h4>Tvarem připomínajícím tělo</h4>
        <div class="example">
            socha, krápník, rampouch, sloup...
        </div>
        <label>Velikost "těla" <input type="number" value="<?= $controller->getSelectedBodySize() ?>"
                                      name="<?= $controller::BODY_SIZE ?>"></label>
        <?php $realTimeOfStatueLikeDestruction = $controller->getRealTimeOfStatueLikeDestruction();
        $fatigueFromStatueLikeDestruction = null;
        try {
            $fatigueFromStatueLikeDestruction = $realTimeOfStatueLikeDestruction->getFatigue();
            ?>
            <div>
                Únava <strong><?= $fatigueFromStatueLikeDestruction->getValue() ?></strong>
            </div>
            <?php
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            ?>
            <div class="error">
                Únava <strong>mimo rozsah</strong>
            </div>
        <?php } ?>
        <div class="panel">
            <h5>Doba ničení "sochy"</h5>
            <?php $realTimeOfStatueLikeDestruction = $controller->getRealTimeOfStatueLikeDestruction()->findTime(Time::HOUR);
            echo $realTimeOfStatueLikeDestruction->getValue() . ' ' . TimeUnitCode::getIt($realTimeOfStatueLikeDestruction->getUnit())->translateTo('cs', $realTimeOfStatueLikeDestruction->getValue()) ?>
        </div>
    </div>

    <div class="panel">
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
        <?php $realTimeOfVoluminousItemDestruction = $controller->getRealTimeOfVoluminousItemDestruction();
        $fatigueFromVoluminousItemDestruction = null;
        try {
            $fatigueFromVoluminousItemDestruction = $realTimeOfVoluminousItemDestruction->getFatigue();
            ?>
            <div>
                Únava <strong><?= $fatigueFromVoluminousItemDestruction->getValue() ?></strong>
            </div>
            <?php
        } catch (CanNotConvertThatBonusToTime $canNotConvertThatBonusToTime) {
            ?>
            <div class="error">
                Únava <strong>mimo rozsah</strong>
            </div>
            <?php
        } ?>
        <div class="panel">
            <h5>Doba ničení něčeho tlustšího</h5>
            <?php $realTimeOfVoluminousItemDestruction = $controller->getRealTimeOfVoluminousItemDestruction()->findTime(Time::HOUR);
            echo $realTimeOfVoluminousItemDestruction->getValue() . ' ' . TimeUnitCode::getIt($realTimeOfVoluminousItemDestruction->getUnit())->translateTo('cs', $realTimeOfVoluminousItemDestruction->getValue()) ?>
        </div>
    </div>
</div>