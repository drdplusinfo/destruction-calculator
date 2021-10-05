<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class DamageBody extends AbstractDestructionBody
{
    private \DrdPlus\DestructionCalculator\CurrentDestruction $currentDestruction;
    private \DrdPlus\DestructionCalculator\CurrentProperties $currentProperties;
    private \DrdPlus\AttackSkeleton\HtmlHelper $htmlHelper;

    public function __construct(DestructionWebPartsContainer $destructionWebPartsContainer, HtmlHelper $htmlHelper)
    {
        $this->currentDestruction = $destructionWebPartsContainer->getCurrentDestruction();
        $this->currentProperties = $destructionWebPartsContainer->getCurrentProperties();
        $this->htmlHelper = $htmlHelper;
    }

    public function getValue(): string
    {
        $strengthInputName = DestructionRequest::STRENGTH;
        $inappropriateToolInputName = DestructionRequest::INAPPROPRIATE_TOOL;
        $rollOnDestructingInputName = DestructionRequest::ROLL_ON_DESTRUCTING;
        return <<<HTML
<div class="row">
    <div class="col">
      <label>
        Síla <span class="note">(Sil)</span>
        <input type="number" min="-40" max="40" value="{$this->currentProperties->getCurrentStrength()}" step="1" name="{$strengthInputName}">
      </label>
    </div>
    <div class="col">
      <label>
        <input type="checkbox" name="{$inappropriateToolInputName}" {$this->getCheckedOfInappropriateTool()} value="1">
        Nevhodný nástroj <span class="note">(-6 k <span class="keyword">Síle ničení</span>)</span>
        {$this->getReasonWhyToolIsInapropriate()}
      </label>
    </div>
    <div class="col">
      <a target="_blank" href="https://pph.drdplus.info/?trial=1#vypocet_sily_niceni">Síla ničení</a>
      <strong>{$this->currentDestruction->getCurrentPowerOfDestruction()->getValue()}</strong>
    </div>
    <div class="col">
      <label>
        <input title="{$this->currentDestruction->getCurrent2d6RollTitle()}" name="{$rollOnDestructingInputName}" type="number" value="{$this->currentDestruction->getCurrentRollOnDestructing()->getValue()}">
        <span class="note">(2k6<span class="upper-index">+</span>)</span>
      </label>
      + {$this->currentDestruction->getCurrentPowerOfDestruction()->getValue()}
      - {$this->currentDestruction->getCurrentMaterialResistance()->getValue()} =
      <strong>{$this->currentDestruction->getCurrentRollOnDestruction()->getValue()}</strong>
      <div>
        <a class="btn btn-info" href="{$this->getUrlToRollAgain()}">
          Hodit znovu na <span class="keyword">Ničení</span> 2k6<span class="upper-index">+</span>
        </a>
      </div>
    </div>
  {$this->getFailureInfo()}
</div>
HTML;
    }

    private function getReasonWhyToolIsInapropriate(): string
    {
        if ($this->currentDestruction->isCurrentWeaponInapropriateTool()) {
            return <<<HTML
<div class="note">Současná zbraň není dobrý nástroj na ničení</div>
HTML;
        }
        return '';
    }

    private function getFailureInfo(): string
    {
        if ($this->currentDestruction->getCurrentRollOnDestruction()->isSuccess()) {
            return '';
        }
        return <<<HTML
<div class="col">
  <div class="alert alert-danger">Předmět <strong>nebyl</strong> poškozen</div>
</div>
HTML;
    }

    private function getUrlToRollAgain(): string
    {
        return $this->htmlHelper->getLocalUrlWithQuery([DestructionRequest::SHOULD_ROLL_ON_DESTRUCTING => 1]);
    }

    private function getCheckedOfInappropriateTool(): string
    {
        return $this->htmlHelper->getChecked($this->currentDestruction->isCurrentToolInappropriate(), true);
    }
}