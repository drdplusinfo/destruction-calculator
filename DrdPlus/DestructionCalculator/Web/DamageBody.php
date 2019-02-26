<?php
declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class DamageBody extends AbstractDestructionBody
{
    /**
     * @var CurrentDestruction
     */
    private $currentDestruction;
    /**
     * @var \DrdPlus\DestructionCalculator\CurrentProperties
     */
    private $currentProperties;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

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
      </label>
    </div>
    <div class="col">
      <a target="_blank" href="https://pph.drdplus.info/#vypocet_sily_niceni">Síla ničení</a>
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
  </div>
HTML;
    }

    private function getUrlToRollAgain(): string
    {
        return $this->htmlHelper->getLocalUrlWithQuery([DestructionRequest::SHOULD_ROLL_ON_DESTRUCTING => 1]);
    }

    private function getCheckedOfInappropriateTool(): string
    {
        return $this->htmlHelper->getChecked($this->currentDestruction->getCurrentInappropriateTool(), true);
    }
}