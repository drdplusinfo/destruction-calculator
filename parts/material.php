<?php
namespace DrdPlus\DestructionCalculator;

/** @var DestructionController $controller */
?>
<div class="row">
  <div class="col">
    <label>Materiál
      <select name="material">
          <?php foreach ($controller->getMaterialCodes() as $materialCode) { ?>
            <option value="<?= $materialCode ?>"
                    <?php if ($controller->getCurrentMaterial()->getValue() === $materialCode->getValue()) { ?>selected="selected"
                <?php } ?>><?= $materialCode->translateTo('cs') ?>
              (pevnost <?= $controller->getMaterialResistance($materialCode) ?>)
            </option>
          <?php } ?>
      </select>
    </label>
  </div>
</div>