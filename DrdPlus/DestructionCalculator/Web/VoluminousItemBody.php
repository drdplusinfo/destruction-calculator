<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class VoluminousItemBody extends AbstractDestructionBody
{
    /**
     * @var array|\DrdPlus\Codes\Units\VolumeUnitCode[]
     */
    private $volumeUnits;
    /**
     * @var CurrentDestruction
     */
    private $currentDestruction;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

    public function __construct(DestructionWebPartsContainer $destructionWebPartsContainer, HtmlHelper $htmlHelper)
    {
        $this->volumeUnits = $destructionWebPartsContainer->getVolumeUnits();
        $this->currentDestruction = $destructionWebPartsContainer->getCurrentDestruction();
        $this->htmlHelper = $htmlHelper;
    }

    public function getValue(): string
    {
        $currentVolumeValue = \number_format($this->currentDestruction->getCurrentVolumeValue(), 2);
        $volumeValueInputName = DestructionRequest::VOLUME_VALUE;
        $volumeUnitInputName = DestructionRequest::VOLUME_UNIT;
        return <<<HTML
<div class="row">
    <div class="col">
      <div class="row">
        <div class="col example">zeď, dveře, led...</div>
      </div>
    </div>
    <div class="col">
        <label>
          Objem ničeného předmětu či jeho části
          <input type="number" value="{$currentVolumeValue}" name="{$volumeValueInputName}">
        </label>
        <div class="example">
          například díra 30 cm x 30 cm v ledu tlustém 25 cm = 30x30x25 = 22500 cm<span class="upper-index">3</span> = 22.5 litru
        </div>
    </div>
    <div class="col">
        <label>
          Jednotka objemu
          <select name="{$volumeUnitInputName}">
              {$this->getVolumeUnitOptions()}
          </select>
        </label>
    </div>
    <div class="col">
       <div class="col alert alert-primary">Únava {$this->getFatigue()}</div>
    </div>
  <div class="col">
    <div class="row">
    <div class="col"><div class="alert alert-primary">Doba ničení {$this->getTimeOfDestruction()}</div></div>
    </div>
  </div>
</div>
HTML;
    }

    private function getFatigue(): string
    {
        $currentFatigueFromVoluminousItemDestruction = $this->currentDestruction->getCurrentFatigueFromVoluminousItemDestruction();
        if (!$currentFatigueFromVoluminousItemDestruction) {
            return $this->getOutOfKnownRangeHtml();
        }
        return <<<HTML
<strong>{$currentFatigueFromVoluminousItemDestruction->getValue()}</strong>
HTML;
    }

    private function getVolumeUnitOptions(): string
    {
        $volumeUnitOptions = [];
        $currentVolumeUnit = $this->currentDestruction->getCurrentVolumeUnit();
        foreach ($this->volumeUnits as $volumeUnit) {
            $selected = $this->htmlHelper->getSelected($volumeUnit, $currentVolumeUnit);
            $volumeUnitOptions[] = <<<HTML
<option value="{$volumeUnit->getValue()}" $selected>{$volumeUnit->translateTo('cs')}</option>
HTML;
        }
        return \implode("\n", $volumeUnitOptions);
    }

    private function getTimeOfDestruction(): string
    {
        $currentTimeOfVoluminousItemDestruction = $this->currentDestruction->getCurrentTimeOfVoluminousItemDestruction();
        if (!$currentTimeOfVoluminousItemDestruction) {
            return $this->getOutOfKnownRangeHtml();
        }
        return <<<HTML
<strong>{$currentTimeOfVoluminousItemDestruction->getValue()} {$currentTimeOfVoluminousItemDestruction->getUnitCode()->translateTo('cs', $currentTimeOfVoluminousItemDestruction->getValue())}
</strong>
HTML;
    }
}