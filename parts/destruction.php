<?php
namespace DrdPlus\Calculators\Destruction;

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

<label>Předmět
    <select name="item-type" id="itemType">
        <option value="generic">Něco obyčejného (třeba stůl)</option>
        <option value="statue">S tělesnými proporcemi (třeba socha)</option>
        <option value="volume">Něco objemného (třeba zeď, tlustší dveře)</option>
    </select>
</label>

<div id="volumeRelated" class="hidden">
    <label>
        Objem ničeného předmětu
        <input type="number" value="0.00" name="volume-value">
    </label>
    <label>
        Jednotka objemu
        <select name="volume-unit">
            <?php foreach ($controller->getVolumeUnits() as $volumeUnit) { ?>
                <option value="<?= $volumeUnit->getValue() ?>"><?= $volumeUnit->getValue() ?></option>
            <?php } ?>
        </select>
    </label>
</div>