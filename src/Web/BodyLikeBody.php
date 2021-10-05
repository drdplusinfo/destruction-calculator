<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class BodyLikeBody extends AbstractDestructionBody
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
        $bodySizeInputName = DestructionRequest::BODY_SIZE;
        return <<<HTML
<div class="row">
  <div class="col">
    <div class="example">socha, krápník, rampouch, sloup...</div>
    <div class="note">
      Toto je pro kompletní zníčení předmětu. Moná ti bude stačit to jen <span class="keyword">zlomit</span>?
    </div>
  </div>
  <div class="col">
  <label>
    <a target="_blank" href="https://pph.drdplus.info/?trial=1#tabulka_velikosti_a_hmotnosti_ras">Velikost "těla"</a>
    <input type="number" value="{$this->currentProperties->getCurrentBodySize()}" name="{$bodySizeInputName}">
  </label>
  <div class="note">přibližně <a href="https://pph.drdplus.info/?trial=1#tabulka_vzdalenosti">(β(šířka) + β(výška))</a> / 2</div>
  </div>
  <div class="col">
    <div class="col alert alert-primary">Únava {$this->getFatigue()}</div>
  </div>
  <div class="col">
    <div class="col alert alert-primary">Doba celkového ničení {$this->getTime()}</div>
  </div>
</div>
HTML;
    }

    private function getFatigue(): string
    {
        $currentFatigueFromStatueLikeDestruction = $this->currentDestruction->getCurrentFatigueFromStatueLikeDestruction();
        if (!$currentFatigueFromStatueLikeDestruction instanceof \DrdPlus\Tables\Measurements\Fatigue\Fatigue) {
            return $this->getOutOfKnownRangeHtml();
        }
        return <<<HTML
<strong>{$currentFatigueFromStatueLikeDestruction->getValue()}</strong>
HTML;
    }

    private function getTime(): string
    {
        $timeOfStatueLikeDestruction = $this->currentDestruction->getCurrentTimeOfStatueLikeDestruction();
        if (!$timeOfStatueLikeDestruction instanceof \DrdPlus\Tables\Measurements\Time\Time) {
            return $this->getOutOfKnownRangeHtml();
        }
        return <<<HTML
<strong>{$timeOfStatueLikeDestruction->getValue()} {$timeOfStatueLikeDestruction->getUnitCode()->translateTo('cs', $timeOfStatueLikeDestruction->getValue())}</strong>
HTML;
    }
}