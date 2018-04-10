<?php
namespace DrdPlus\Calculators\Destruction;

/** @var Controller $controller */
?>
<hr>
<?php include __DIR__ . '/material.php';
include __DIR__ . '/damage.php';
if (!$controller->getRollOnDestruction()->isSuccess()) { ?>
    <div class="block note">Zatím žádný čas ani únava z ničení</div>
    <?php return;
} ?>
<div class="block">
    <?php include __DIR__ . '/basic_item.php';
    include __DIR__ . '/body_like.php';
    include __DIR__ . '/voluminous_item.php';
    ?>
</div>