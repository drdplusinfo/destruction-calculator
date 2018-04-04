<?php
namespace DrdPlus\Calculators\Destruction;

/** @var Controller $controller */
?>
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