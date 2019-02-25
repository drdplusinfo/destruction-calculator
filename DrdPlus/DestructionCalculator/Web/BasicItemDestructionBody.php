<?php
declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class BasicItemDestructionBody extends StrictObject implements BodyInterface
{
    /**
     * @var CurrentDestruction
     */
    private $currentDestruction;
    /**
     * @var \DrdPlus\DestructionCalculator\CurrentProperties
     */
    private $currentProperties;

    public function __construct(DestructionWebPartsContainer $destructionWebPartsContainer)
    {
        $this->currentDestruction = $destructionWebPartsContainer->getCurrentDestruction();
        $this->currentProperties = $destructionWebPartsContainer->getCurrentProperties();
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return <<<HTML
        <div class="row">
  <div class="col">
    <div class="example">hlava sochy, dlaždice, meč, lopata...</div>
      {$this->getFatigueFromItemSize()}
  </div>
  <div class="col">
    Doba ničení
      {$this->getTimeOfDestruction()}
  </div>
</div>
HTML;
    }

    private function getFatigueFromItemSize(): string
    {
        $fatigueFromBasicItemDestruction = $this->currentDestruction->getCurrentBasicItemDestructionFatigue();
        if (!$fatigueFromBasicItemDestruction) {
            return <<<HTML
<div class="error">
  Únava <strong>mimo rozsah</strong>
</div>
HTML;
        }
        $itemSizeInputName = DestructionRequest::ITEM_SIZE;
        return <<<HTML
<label>Velikost věci <input type="number" value="{$this->currentProperties->getCurrentItemSize()}"
    name="{$itemSizeInputName}"></label>
<div>
  Únava <strong>{$fatigueFromBasicItemDestruction->getValue()}</strong>
</div>
HTML;
    }

    private function getTimeOfDestruction()
    {
        $currentTimeOfBasicItemDestruction = $this->currentDestruction->getCurrentTimeOfBasicItemDestruction();
        if (!$currentTimeOfBasicItemDestruction) {
            return <<<HTML
<div class="error">
  Doba <strong>mimo rozsah</strong>
</div>
HTML;
        }
        $timeHumanName = $currentTimeOfBasicItemDestruction->getUnitCode()->translateTo('cs', $currentTimeOfBasicItemDestruction->getValue());
        return <<<HTML
<strong>{$currentTimeOfBasicItemDestruction->getValue()} {$timeHumanName}</strong>
HTML;
    }
}