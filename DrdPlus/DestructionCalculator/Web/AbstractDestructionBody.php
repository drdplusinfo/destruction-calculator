<?php
declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

abstract class AbstractDestructionBody extends StrictObject implements BodyInterface
{
    public function __toString()
    {
        return $this->getValue();
    }

    protected function getOutOfKnownRangeHtml(): string
    {
        return <<<HTML
<div class="alert-warning"><strong>mimo známý rozsah</strong></div>
HTML;
    }
}