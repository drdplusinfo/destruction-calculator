<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var DestructionController $controller */
?>
<div class="row">
  <h4 class="col">Musí se to rozmlátit</h4>
</div>
<div class="row">
  <div class="col">
    <div class="example">
      socha, krápník, rampouch, sloup...
    </div>
    <div class="note">
      Toto je pro kompletní zníčení předmětu. Moná ti bude stačit to jen <span class="keyword">zlomit</span>?
    </div>
    <label><a target="_blank" href="https://pph.drdplus.info/#tabulka_velikosti_a_hmotnosti_ras">Velikost "těla"</a>
      <input type="number" value="<?= $controller->getCurrentBodySize() ?>"
             name="<?= $controller::BODY_SIZE ?>"></label>
    <span class="note">přibližně <a href="https://pph.drdplus.info/#tabulka_vzdalenosti">(β(šířka) + β(výška))</a> / 2</span>
      <?php $fatigueFromStatueLikeDestruction = $controller->getFatigueFromStatueLikeDestruction();
      if (!$fatigueFromStatueLikeDestruction) { ?>
        <div class="error">Únava <strong>mimo rozsah</strong></div>
      <?php } else { ?>
        <div>Únava z celkového ničení <strong><?= $fatigueFromStatueLikeDestruction->getValue() ?></strong></div>
      <?php } ?>
    <div class="panel">
      Doba celkového ničení
        <?php $timeOfStatueLikeDestruction = $controller->getTimeOfStatueLikeDestruction();
        if (!$timeOfStatueLikeDestruction) { ?>
          <div class="error">Doba ničení <strong>mimo rozsah</strong></div>
        <?php } else { ?>
          <strong>
              <?= $timeOfStatueLikeDestruction->getValue()
              . ' ' . TimeUnitCode::getIt($timeOfStatueLikeDestruction->getUnit())->translateTo('cs', $timeOfStatueLikeDestruction->getValue()) ?>
          </strong>
        <?php } ?>
    </div>
  </div>
</div>