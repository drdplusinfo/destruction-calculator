<?php
namespace DrdPlus\Calculators\Destruction;

/** @var DestructionController $controller */
?>
<div class="row">
  <h4 class="col">Poškození</h4>
</div>
<div class="row">
  <div class="col">
    <label>Síla<input type="number" value="<?= $controller->getCurrentStrength() ?>" step="1"
                      name="<?= $controller::STRENGTH ?>"></label>
      <?php include __DIR__ . '/../vendor/drd-plus/attack-skeleton/parts/melee_weapon.php' ?>
    <div class="panel">
      <label>
        <input type="checkbox" name="<?= $controller::INAPPROPRIATE_TOOL ?>"
               value="1" <?php if ($controller->getCurrentInappropriateTool()) { ?>checked="checked"<?php } ?>
        >
        Nevhodný nástroj <span class="note">(-6 k <span class="keyword">Síle ničení</span>)</span>
      </label>
    </div>
    <div class="block">
      <div>
        <a target="_blank" href="https://pph.drdplus.info/#vypocet_sily_niceni">Síla ničení</a>
        <strong><?= $controller->getPowerOfDestruction()->getValue() ?></strong>
      </div>
      <div>
        <label>Hod na ničení 2k6<span class="upper-index">+</span>
          <input name="<?= $controller::ROLL_ON_DESTRUCTING ?>" type="number"
                 value="<?= $controller->getCurrentRollOnDestructing()->getValue() ?>">
        </label>
        <button type="submit" name="<?= $controller::SHOULD_ROLL_ON_DESTRUCTING ?>" value="1" class="manual">Hodit
          2k6<span class="upper-index">+</span>
        </button>
        + <?= $controller->getPowerOfDestruction()->getValue() ?>
        - <?= $controller->getMaterialResistance($controller->getCurrentMaterial())->getValue() ?> =
        <strong><?= $controller->getRollOnDestruction()->getValue() ?></strong>
      </div>
        <?php if (!$controller->getRollOnDestruction()->isSuccess()) { ?>
          <div class="info-messages">
            <div class="info-message">Předmět <strong>nebyl</strong> poškozen</div>
          </div>
        <?php } ?>
    </div>
  </div>
</div>