<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use Granam\Strict\Object\StrictObject;

class TablesRequestDetector extends StrictObject
{
    private \DrdPlus\RulesSkeleton\RulesUrlMatcher $rulesUrlMatcher;
    private \DrdPlus\RulesSkeleton\Request $request;

    public function __construct(RulesUrlMatcher $rulesUrlMatcher, Request $request)
    {
        $this->rulesUrlMatcher = $rulesUrlMatcher;
        $this->request = $request;
    }

    public function areTablesRequested(): bool
    {
        return $this->rulesUrlMatcher->match($this->request->getCurrentUrl())->getRouteName() === 'tables'
            || $this->rulesUrlMatcher->match($this->request->getCurrentUrl())->getRouteName() === 'tables_with_query'
            || $this->request->areTablesRequested();
    }
}
