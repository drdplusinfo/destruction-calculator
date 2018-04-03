<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Tables\Tables;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

$controller = new Controller(Tables::getIt());
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/generic/graphics.css" rel="stylesheet" type="text/css">
    <link href="css/generic/skeleton.css" rel="stylesheet" type="text/css">
    <link href="css/generic/issues.css" rel="stylesheet" type="text/css">
    <link href="css/attack-skeleton/attack-skeleton.css" rel="stylesheet" type="text/css">
    <link href="css/destruction.css" rel="stylesheet" type="text/css">
    <noscript>
        <link rel="stylesheet" type="text/css" href="css/generic/no_script.css">
    </noscript>
</head>
<body>
<div id="fb-root"></div>
<div class="background"></div>
<form class="block delete" action="/" method="post" onsubmit="return window.confirm('Opravdu smazat včetně historie?')">
    <label>
        <input type="submit" value="Smazat" name="<?= $controller::DELETE_HISTORY ?>" class="manual">
        <span class="hint">(včetně dlouhodobé paměti)</span>
    </label>
</form>
<form class="block" action="" method="get" id="configurator">
    <div class="block remember">
        <label><input type="checkbox" name="<?= $controller::REMEMBER_CURRENT ?>" value="1"
                      <?php if ($controller->shouldRemember()) { ?>checked="checked"<?php } ?>>
            Pamatovat <span class="hint">(i při zavření prohlížeče)</span></label>
    </div>
    <div class="block">
        <div class="panel"><?php include __DIR__ . '/parts/destruction.php'; ?></div>
    </div>
</form>
<div class="block">
    <hr>
    <a target="_blank" href="https://pph.drdplus.info/#niceni">Pravidla pro ničení v PPH</a>
</div>
<div class="block issues">
    <hr>
    <a href="https://rpgforum.cz/forum/viewtopic.php?f=238&t=14870"><img src="images/generic/rpgforum-ico.png">
        Máš nápad 😀? Vidíš chybu 😱?️ Sem s tím!
    </a>
    <a class="float-right" href="https://github.com/jaroslavtyc/drd-plus-calculators-destruction/"
       title="Fork me on GitHub"><img class="github" src="/images/generic/GitHub-Mark-64px.png"></a>
</div>
<script type="text/javascript" src="js/generic/main.js"></script>
<script type="text/javascript" src="js/destruction.js"></script>
</body>
</html>
