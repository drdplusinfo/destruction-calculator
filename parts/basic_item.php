<?php
namespace DrdPlus\DestructionCalculator;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var DestructionController $controller */
?>
<div class="row">
  <h4 class="col">Stačí to zlomit</h4>
</div>
<div class="row">
  <div class="col">
    <div class="example">hlava sochy, dlaždice, meč, lopata...</div>
      <?php $fatigueFromBasicItemDestruction = $controller->getBasicItemDestructionFatigue();
      if (!$fatigueFromBasicItemDestruction) { ?>
        <div class="error">
          Únava <strong>mimo rozsah</strong>
        </div>
      <?php } else { ?>
        <label>Velikost věci <input type="number" value="<?= $controller->getCurrentItemSize() ?>"
                                    name="<?= $controller::ITEM_SIZE ?>"></label>
        <div>
          Únava <strong><?= $fatigueFromBasicItemDestruction->getValue() ?></strong>
        </div>
      <?php } ?>
  </div>
  <div class="col">
    Doba ničení
      <?php $realTimeOfBasicItemDestruction = $controller->getTimeOfBasicItemDestruction();
      if (!$realTimeOfBasicItemDestruction) { ?>
        <div class="error">
          Doba <strong>mimo rozsah</strong>
        </div>
      <?php } else { ?>
        <strong>
            <?= $realTimeOfBasicItemDestruction->getValue()
            . ' ' . TimeUnitCode::getIt($realTimeOfBasicItemDestruction->getUnit())->translateTo('cs', $realTimeOfBasicItemDestruction->getValue()) ?>
        </strong>
      <?php } ?>
  </div>
</div>