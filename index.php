<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Tables\Tables;

include_once __DIR__ . '/vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', '1');

/** @noinspection PhpUnusedLocalVariableInspection */
$controller = new DestructionController(Tables::getIt());
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/generic/vendor/bootstrap.4.0.0/bootstrap-reboot.min.css" rel="stylesheet" type="text/css">
    <link href="css/generic/vendor/bootstrap.4.0.0/bootstrap-grid.min.css" rel="stylesheet" type="text/css">
    <link href="css/generic/vendor/bootstrap.4.0.0/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/generic/graphics.css" rel="stylesheet" type="text/css">
    <link href="css/generic/skeleton.css" rel="stylesheet" type="text/css">
    <link href="css/generic/issues.css" rel="stylesheet" type="text/css">
    <link href="css/attack/skeleton.css" rel="stylesheet" type="text/css">
    <link href="css/destruction.css" rel="stylesheet" type="text/css">
    <noscript>
      <link rel="stylesheet" type="text/css" href="css/generic/no_script.css">
    </noscript>
  </head>
  <body class="container">
    <div class="background"></div>
      <?php include __DIR__ . '/vendor/drd-plus/calculator-skeleton/history_deletion.php' ?>
    <div class="row">
      <hr class="col">
    </div>
    <form action="" method="get" id="configurator">
        <?php
        include __DIR__ . '/parts/material.php';
        include __DIR__ . '/parts/damage.php';
        include __DIR__ . '/parts/basic_item.php';
        include __DIR__ . '/parts/body_like.php';
        include __DIR__ . '/parts/voluminous_item.php';
        ?>
    </form>
    <div class="row">
      <hr class="col">
    </div>
    <div class="row">
      <a class="col" target="_blank" href="https://pph.drdplus.info/#niceni">Pravidla pro ničení v PPH</a>
    </div>
      <?php
      /** @noinspection PhpUnusedLocalVariableInspection */
      $sourceCodeUrl = 'https://github.com/jaroslavtyc/drd-plus-calculators-destruction';
      include __DIR__ . '/vendor/drd-plus/calculator-skeleton/issues.php' ?>
    <script type="text/javascript" src="js/generic/skeleton.js"></script>
    <script type="text/javascript" src="js/destruction.js"></script>
  </body>
</html>
