<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Codes\Units\TimeUnitCode;

/** @var Controller $controller */
?>
<div class="panel">
    <h4>Tvarem připomínajícím tělo</h4>
    <div class="example">
        socha, krápník, rampouch, sloup...
    </div>
    <label>Velikost "těla" <input type="number" value="<?= $controller->getSelectedBodySize() ?>"
                                  name="<?= $controller::BODY_SIZE ?>"></label>
    <?php $fatigueFromStatueLikeDestruction = $controller->getFatigueFromStatueLikeDestruction();
    if (!$fatigueFromStatueLikeDestruction) { ?>
        <div class="error">Únava <strong>mimo rozsah</strong></div>
    <?php } else { ?>
        <div>Únava <strong><?= $fatigueFromStatueLikeDestruction->getValue() ?></strong></div>
    <?php } ?>
    <div class="panel">
        Doba ničení <span class="keyword">sochy</span>
        <?php $timeOfStatueLikeDestruction = $controller->getTimeOfStatueLikeDestruction();
        if (!$timeOfStatueLikeDestruction) { ?>
            <div class="error">Doba ničení <strong>mimo rozsah</strong></div>
        <?php } else { ?>
            <strong><?= $timeOfStatueLikeDestruction->getValue() . ' ' . TimeUnitCode::getIt($timeOfStatueLikeDestruction->getUnit())->translateTo('cs', $timeOfStatueLikeDestruction->getValue()) ?></strong>
        <?php } ?>
    </div>
</div>