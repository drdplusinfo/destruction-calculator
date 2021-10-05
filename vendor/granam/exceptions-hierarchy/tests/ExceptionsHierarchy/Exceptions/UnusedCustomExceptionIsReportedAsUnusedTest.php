<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions;

class UnusedCustomExceptionIsReportedAsUnusedTest extends AbstractExceptionsHierarchyTest
{
    protected function getTestedNamespace(): string
    {
        return __NAMESPACE__ . '\\DummyExceptionsHierarchy\\UnusedCustomException';
    }

    protected function getRootNamespace(): string
    {
        return $this->getTestedNamespace();
    }

    protected function getExceptionsSubDir(): string
    {
        return ''; // exceptions are directly in the tested namespace
    }

    /**
     * @test
     */
    public function My_exceptions_are_used()
    {
        $this->expectException(\Granam\ExceptionsHierarchy\Exceptions\UnusedException::class);
        $this->expectExceptionMessageMatches('~DoesAnybodyWantMe~');
        // overloaded parent annotation
        parent::My_exceptions_are_used();
    }

}
