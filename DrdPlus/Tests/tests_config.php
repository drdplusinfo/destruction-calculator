<?php
global $testsConfiguration;
$testsConfiguration = new \DrdPlus\Tests\FrontendSkeleton\TestsConfiguration();
$testsConfiguration->disableHasCustomBodyContent();
$testsConfiguration->disableHasTables();
$testsConfiguration->setExpectedWebName('DrD+ kalkulátor ničení');
$testsConfiguration->setExpectedPageTitle('DrD+ kalkulátor ničení');
$testsConfiguration->disableHasMoreVersions();
$testsConfiguration->disableHasLinksToAltar();