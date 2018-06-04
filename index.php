<?php
namespace DrdPlus\DestructionCalculator;

use DrdPlus\Tables\Tables;

\error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    \ini_set('display_errors', '1');
} else {
    \ini_set('display_errors', '0');
}

$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());
$vendorRoot = $vendorRoot ?? $documentRoot . '/vendor';

require_once $vendorRoot . '/autoload.php';

/** @noinspection PhpUnusedLocalVariableInspection */
$controller = new DestructionController(
    Tables::getIt(),
    'https://github.com/jaroslavtyc/drd-plus-calculators-destruction',
    __DIR__ /* document root */,
    $vendorRoot
);

require $vendorRoot . '/drd-plus/attack-skeleton/index.php';