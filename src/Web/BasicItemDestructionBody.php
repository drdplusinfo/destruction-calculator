<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class BasicItemDestructionBody extends AbstractDestructionBody
{
    private \DrdPlus\DestructionCalculator\CurrentDestruction $currentDestruction;
    private \DrdPlus\DestructionCalculator\CurrentProperties $currentProperties;

    public function __construct(DestructionWebPartsContainer $destructionWebPartsContainer)
    {
        $this->currentDestruction = $destructionWebPartsContainer->getCurrentDestruction();
        $this->currentProperties = $destructionWebPartsContainer->getCurrentProperties();
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="row">
  <div class="col">
    <div class="row">
      <div class="col example">lano, dlaždice, meč, lopata, hlava sochy...</div>
    </div>
  </div>
  <div class="col">{$this->getItemSize()}</div>
  <div class="col"><div class="col alert alert-primary">Únava {$this->getFatigueFromItemSize()}</div></div>
  <div class="col"><div class="col alert alert-primary">Doba ničení {$this->getTimeOfDestruction()}</div></div>
</div>
HTML;
    }

    private function getFatigueFromItemSize(): string
    {
        $fatigueFromBasicItemDestruction = $this->currentDestruction->getCurrentBasicItemDestructionFatigue();
        if ($fatigueFromBasicItemDestruction === null) {
            return $this->getOutOfKnownRangeHtml();
        }
        return <<<HTML
<strong>{$fatigueFromBasicItemDestruction->getValue()}</strong>
HTML;
    }

    private function getItemSize(): string
    {
        $itemSizeInputName = DestructionRequest::ITEM_SIZE;
        return <<<HTML
<label>Velikost věci <input type="number" value="{$this->currentProperties->getCurrentItemSize()}"
    name="{$itemSizeInputName}"></label>
HTML;
    }

    private function getTimeOfDestruction(): string
    {
        $currentTimeOfBasicItemDestruction = $this->currentDestruction->getCurrentTimeOfBasicItemDestruction();
        if ($currentTimeOfBasicItemDestruction === null) {
            return $this->getOutOfKnownRangeHtml();
        }
        $timeHumanName = $currentTimeOfBasicItemDestruction->getUnitCode()->translateTo('cs', $currentTimeOfBasicItemDestruction->getValue());
        return <<<HTML
<strong>{$currentTimeOfBasicItemDestruction->getValue()} {$timeHumanName}</strong>
HTML;
    }
}