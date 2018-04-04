<?php
namespace DrdPlus\Calculators\Destruction;

/** @var Controller $controller */
?>
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