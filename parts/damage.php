<?php
namespace DrdPlus\DestructionCalculator;

/** @var DestructionController $controller */
?>
  <div class="row">
    <h4 class="col">Poškození</h4>
  </div>
    <?php include __DIR__ . '/../vendor/drd-plus/attack-skeleton/parts/attack-skeleton/melee_weapon.php' ?>
  <div class="row">
    <div class="col">
      <label>
        Síla <span class="note">(Sil)</span>
        <input type="number" value="<?= $controller->getCurrentStrength() ?>" step="1"
               name="<?= $controller::STRENGTH ?>">
      </label>
    </div>
    <div class="col">
      <label>
        <input type="checkbox" name="<?= $controller::INAPPROPRIATE_TOOL ?>"
               value="1" <?php if ($controller->getCurrentInappropriateTool()) { ?>checked="checked"<?php } ?>
        >
        Nevhodný nástroj <span class="note">(-6 k <span class="keyword">Síle ničení</span>)</span>
      </label>
    </div>
    <div class="col">
      <a target="_blank" href="https://pph.drdplus.info/#vypocet_sily_niceni">Síla ničení</a>
      <strong><?= $controller->getPowerOfDestruction()->getValue() ?></strong>
    </div>
    <div class="col">
      <label title="<?= $controller->getCurrent2d6RollTitle() ?>">
        <input name="<?= $controller::ROLL_ON_DESTRUCTING ?>" type="number"
               value="<?= $controller->getCurrentRollOnDestructing()->getValue() ?>">
        <span class="note">(2k6<span class="upper-index">+</span>)</span>
      </label>
      + <?= $controller->getPowerOfDestruction()->getValue() ?>
      - <?= $controller->getMaterialResistance($controller->getCurrentMaterial())->getValue() ?> =
      <strong><?= $controller->getRollOnDestruction()->getValue() ?></strong>
      <div>
        <button type="submit" name="<?= $controller::SHOULD_ROLL_ON_DESTRUCTING ?>" value="1" class="manual">
          Hodit znovu na ničení 2k6<span class="upper-index">+</span>
        </button>
      </div>
    </div>
  </div>
    <?php if (!$controller->getRollOnDestruction()->isSuccess()) { ?>
  <div class="row">
    <div class="col info-message">Předmět <strong>nebyl</strong> poškozen</div>
  </div>
<?php } ?>