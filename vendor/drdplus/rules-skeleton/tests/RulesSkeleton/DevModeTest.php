<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;

class DevModeTest extends AbstractContentTest
{

    /**
     * @test
     */
    public function I_see_content_marked_by_development_classes(): void
    {
        if (!$this->isRulesSkeletonChecked()) {
            self::assertFalse(false, 'Intended for skeleton only');

            return;
        }
        $html = $this->getRulesForDevHtmlDocument();
        self::assertGreaterThan(
            0,
            $html->getElementsByClassName('covered-by-code')->count(),
            'No "covered-by-code" class has been found'
        );
        self::assertGreaterThan(
            0,
            $html->getElementsByClassName('generic')->count(),
            'No "generic" class has been found'
        );
        self::assertGreaterThan(
            0,
            $html->getElementsByClassName('excluded')->count(),
            'No "excluded" class has been found"'
        );
    }
}
