<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton\Cache;

use Granam\Strict\Object\StrictObject;

class ContentIrrelevantParametersFilter extends StrictObject
{
    private array $contentIrrelevantParameterNames;

    public function __construct(array $contentIrrelevantParameterNames)
    {
        $this->contentIrrelevantParameterNames = \array_fill_keys($contentIrrelevantParameterNames, '');
    }

    public function filterContentIrrelevantParameters(array $parameters): array
    {
        return \array_diff_key($parameters, $this->contentIrrelevantParameterNames);
    }
}
