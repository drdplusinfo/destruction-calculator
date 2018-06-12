<?php
namespace DrdPlus\DestructionCalculator;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var DestructionController $controller */
?>
<div class="row">
  <h4 class="col">Chce to díru</h4>
</div>
<div class="row">
  <div class="col">
    <div class="example">zeď, dveře, led...</div>
    <label>
      Objem ničeného předmětu či jeho části
      <input type="number" value="<?= number_format($controller->getCurrentVolumeValue(), 2); ?>"
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
                    <?php if ($controller->getCurrentVolumeUnit()->getValue() === $volumeUnit->getValue()) { ?>selected="selected"<?php } ?>><?= $volumeUnit->translateTo('cs') ?></option>
          <?php } ?>
      </select>
    </label>
      <?php $fatigueFromVoluminousItemDestruction = $controller->getFatigueFromVoluminousItemDestruction();
      if (!$fatigueFromVoluminousItemDestruction) { ?>
        <div class="error">Únava <strong>mimo rozsah</strong></div>
      <?php } else { ?>
        <div>Únava <strong><?= $fatigueFromVoluminousItemDestruction->getValue() ?></strong></div>
      <?php } ?>
  </div>
  <div class="col">
    Doba ničení
      <?php $realTimeOfVoluminousItemDestruction = $controller->getTimeOfVoluminousItemDestruction();
      if (!$realTimeOfVoluminousItemDestruction) { ?>
        <div class="error">Doba ničení <strong>mimo rozsah</strong></div>
      <?php } else { ?>
        <strong>
            <?= $realTimeOfVoluminousItemDestruction->getValue()
            . ' ' . TimeUnitCode::getIt($realTimeOfVoluminousItemDestruction->getUnit())->translateTo('cs', $realTimeOfVoluminousItemDestruction->getValue()) ?>
        </strong>
      <?php } ?>
  </div>
</div>