<?php declare(strict_types=1);

namespace Tests\DrdPlus\RulesSkeleton;

use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\CookiesService;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\RouteMatch;
use DrdPlus\RulesSkeleton\ServicesContainer;
use Tests\DrdPlus\RulesSkeleton\Partials\AbstractContentTest;
use Mockery\MockInterface;

class ServicesContainerTest extends AbstractContentTest
{
    /**
     * @test
     */
    public function I_can_get_web_versions(): void
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper());
        self::assertNotEmpty($servicesContainer->getCurrentWebVersion());
    }

    /**
     * @test
     */
    public function I_can_get_web_files(): void
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper());
        self::assertNotEmpty($servicesContainer->getRoutedWebFiles());
    }

    /**
     * @test
     */
    public function I_can_get_request(): void
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper());
        self::assertNotEmpty($servicesContainer->getRequest());
    }

    /**
     * @test
     */
    public function I_can_get_rules_url_matcher(): void
    {
        /** @var Dirs|MockInterface $dirs */
        $dirs = $this->mockery(Dirs::class);
        $dirs->shouldReceive('getProjectRoot')
            ->andReturn(__DIR__ . '/router');
        $dirs->shouldReceive('getCacheRoot')
            ->andReturn(sys_get_temp_dir() . '/' . uniqid(__FUNCTION__, true));
        $servicesContainerClass = static::getSutClass();
        $configuration = $this->createCustomConfiguration(
            [Configuration::APPLICATION => [Configuration::YAML_FILE_WITH_ROUTES => basename(__DIR__ . '/router/test_routes.yml')]],
            $dirs
        );
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($configuration, $this->getEnvironment(), $this->getHtmlHelper());
        $rulesUrlMatcher = $servicesContainer->getRulesUrlMatcher();
        self::assertNotEmpty($rulesUrlMatcher);
        self::assertEquals(
            new RouteMatch([
                '_route' => 'root',
                'path' => 'default_frmol',
                'resource' => null,
                'type' => null,
                'prefix' => null,
                'host' => null,
                'schemes' => null,
                'methods' => null,
            ]),
            $rulesUrlMatcher->match('/')
        );
        if ($this->isSkeletonChecked()) {
            self::assertEquals(
                new RouteMatch([
                    '_route' => 'root',
                    'path' => 'routed',
                    'resource' => null,
                    'type' => null,
                    'prefix' => null,
                    'host' => null,
                    'schemes' => null,
                    'methods' => null,
                ]),
                $rulesUrlMatcher->match('/routed')
            );
        }
        $testCacheDirEscaped = escapeshellarg($servicesContainer->getPassedWebCache()->getCacheDir());
        exec("rm -fr $testCacheDirEscaped");
    }

    /**
     * @test
     */
    public function I_can_get_rules_url_matcher_even_if_no_routes_are_defined(): void
    {
        $configuration = $this->createCustomConfiguration([
            Configuration::APPLICATION => [
                Configuration::YAML_FILE_WITH_ROUTES => null,
                Configuration::DEFAULT_YAML_FILE_WITH_ROUTES => 'non-existing.yml',
            ],
        ]);
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($configuration, $this->getEnvironment(), $this->getHtmlHelper());
        $rulesUrlMatcher = $servicesContainer->getRulesUrlMatcher();
        self::assertNotEmpty($rulesUrlMatcher);
        self::assertEquals(new RouteMatch(['path' => '/']), $rulesUrlMatcher->match('/anything'));
    }

    /**
     * @test
     */
    public function I_will_get_rules_url_matcher_with_routes_if_route_file_matches_default(): void
    {
        $configuration = $this->createCustomConfiguration([
            Configuration::APPLICATION => [
                Configuration::YAML_FILE_WITH_ROUTES => null,
            ],
        ]);
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($configuration, $this->getEnvironment(), $this->getHtmlHelper());
        $rulesUrlMatcher = $servicesContainer->getRulesUrlMatcher();
        self::assertNotEmpty($rulesUrlMatcher);
        self::assertEquals(new RouteMatch(['path' => 'something', '_route' => 'root']), $rulesUrlMatcher->match('/something'));
    }

    /**
     * @test
     */
    public function I_can_get_page_cache_with_properly_set_production_mode(): void
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->createEnvironment('prod'), $this->getHtmlHelper());
        self::assertTrue($servicesContainer->getPassedWebCache()->isInProduction(), 'Expected page cache to be in production mode');
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->createEnvironment(), $this->getHtmlHelper());
        self::assertFalse($servicesContainer->getPassedWebCache()->isInProduction(), 'Expected page cache to be not in production mode');
    }

    /**
     * @test
     */
    public function I_can_get_cookies_service(): void
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper());
        self::assertEquals(new CookiesService($servicesContainer->getRequest()), $servicesContainer->getCookiesService());
    }

    /**
     * @test
     */
    public function I_can_get_table_request_detector_even_if_no_route_file()
    {
        $servicesContainer = new class($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper()) extends ServicesContainer {
            protected function getYamlFileWithRoutes(): string
            {
                return '';
            }
        };
        $tablesRequestDetector = $servicesContainer->getTablesRequestDetector();
        self::assertNotEmpty($tablesRequestDetector);
    }

    /**
     * @test
     */
    public function Root_web_files_differs_to_routed_one()
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper());
        $rootWebFiles = $servicesContainer->getRootWebFiles();
        $routedWebFiles = $servicesContainer->getRoutedWebFiles();
        self::assertNotEquals($rootWebFiles, $routedWebFiles);
    }

    /**
     * @test
     */
    public function Root_web_parts_container_differs_to_routed_one()
    {
        $servicesContainerClass = static::getSutClass();
        /** @var ServicesContainer $servicesContainer */
        $servicesContainer = new $servicesContainerClass($this->getConfiguration(), $this->getEnvironment(), $this->getHtmlHelper());
        $rootWebPartsContainer = $servicesContainer->getRootWebPartsContainer();
        $routedWebPartsContainer = $servicesContainer->getRoutedWebPartsContainer();
        self::assertNotEquals($rootWebPartsContainer, $routedWebPartsContainer);
    }
}
