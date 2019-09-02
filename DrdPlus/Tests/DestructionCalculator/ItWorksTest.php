<?php declare(strict_types=1);

namespace DrdPlus\Tests\DestructionCalculator;

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
        require __DIR__ . '/../../../index.php';
        $content = ob_get_clean();
        self::assertRegExp('~^<!DOCTYPE html>\n.+</html>$~s', $content);
    }
}