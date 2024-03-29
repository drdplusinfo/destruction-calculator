<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton\Web;

use DrdPlus\RulesSkeleton\Web\Head;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Granam\WebContentBuilder\HtmlDocument;

class HeadTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function Head_contains_google_analytics_with_id(): void
    {
        $servicesContainer = $this->createServicesContainer(null, $this->createEnvironment('prod'));
        $head = $servicesContainer->getHead();
        $headString = $head->getValue();
        $htmlDocument = new HtmlDocument(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<title>Foo</title>
{$headString}
</head>
</html>
HTML
        );
        $googleAnalytics = $htmlDocument->getElementById('googleAnalyticsId');
        self::assertNotEmpty($googleAnalytics, 'Missing Google analytics ID');
        $src = $googleAnalytics->getAttribute('src');
        self::assertNotEmpty($src);
        $parsed = \parse_url($src);
        $queryString = \urldecode($parsed['query'] ?? '');
        self::assertSame('id=' . $this->getConfiguration()->getGoogleAnalyticsId(), $queryString);
    }

    /**
     * @test
     */
    public function Head_does_not_contain_google_analytics_if_not_in_production(): void
    {
        $servicesContainer = $this->createServicesContainer(); // non-production mode will be detected
        $head = $servicesContainer->getHead();
        $headString = $head->getValue();
        $htmlDocument = new HtmlDocument(<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<title>Foo</title>
{$headString}
</head>
</html>
HTML
        );
        $googleAnalytics = $htmlDocument->getElementById('googleAnalyticsId');
        self::assertEmpty($googleAnalytics, 'Google analytics has not been expected on non-production mode');
    }

    /**
     * @test
     */
    public function I_can_set_own_page_name(): void
    {
        /** @var Head $headClass */
        $headClass = static::getSutClass();
        $servicesContainer = $this->createServicesContainer();
        /** @var Head $head */
        $head = new $headClass(
            $servicesContainer->getConfiguration(),
            $servicesContainer->getHtmlHelper(),
            $servicesContainer->getEnvironment(),
            $servicesContainer->getCssFiles(),
            $servicesContainer->getJsFiles(),
            $pageName = 'foo BAR'
        );
        $expectedPageTitle = \trim($this->getConfiguration()->getTitleSmiley() . ' ' . $pageName);
        self::assertSame($expectedPageTitle, $head->getPageTitle());
        self::assertStringContainsString("<title>$expectedPageTitle</title>", $head->getValue());
    }
}
