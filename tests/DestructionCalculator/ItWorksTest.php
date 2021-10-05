<?php declare(strict_types=1);

namespace Tests\DrdPlus\DestructionCalculator;

use PHPUnit\Framework\TestCase;

class ItWorksTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_load_it_without_error(): void
    {
        $_SERVER['QUERY_STRING'] = '';
        ob_start();
        require DRD_PLUS_INDEX_FILE_NAME_TO_TEST;
        $content = ob_get_clean();
        self::assertMatchesRegularExpression('~^<!DOCTYPE html>\n.+</html>$~s', $content);
    }
}
