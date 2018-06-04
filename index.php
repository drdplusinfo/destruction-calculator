<?php
namespace DrdPlus\Calculators\Destruction;

use DrdPlus\Tables\Tables;

\error_reporting(-1);
if ((!empty($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') || PHP_SAPI === 'cli') {
    \ini_set('display_errors', '1');
} else {
    \ini_set('display_errors', '0');
}

$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());
$vendorRoot = $vendorRoot ?? $documentRoot . '/vendor';
/** @noinspection PhpUnusedLocalVariableInspection */
$partsRoot = \file_exists($documentRoot . '/parts')
    ? ($documentRoot . '/parts')
    : ($vendorRoot . '/drd-plus/attack-skeleton/parts');

include_once __DIR__ . '/vendor/autoload.php';

/** @noinspection PhpUnusedLocalVariableInspection */
$controller = new DestructionController(
    __DIR__ /* document root */,
    'https://github.com/jaroslavtyc/drd-plus-calculators-destruction',
    Tables::getIt()
);

require __DIR__ . '/vendor/drd-plus/attack-skeleton/index.php';