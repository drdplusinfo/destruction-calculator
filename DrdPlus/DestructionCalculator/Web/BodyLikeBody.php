<?php
declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionRequest;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;
use Granam\Strict\Object\StrictObject;
use Granam\WebContentBuilder\Web\BodyInterface;

class BodyLikeBody extends StrictObject implements BodyInterface
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
        $bodySizeInputName = DestructionRequest::BODY_SIZE;
        return <<<HTML
<div class="row">
  <div class="col">
    <div class="example">
      socha, krápník, rampouch, sloup...
    </div>
    <div class="note">
      Toto je pro kompletní zníčení předmětu. Moná ti bude stačit to jen <span class="keyword">zlomit</span>?
    </div>
    <label><a target="_blank" href="https://pph.drdplus.info/#tabulka_velikosti_a_hmotnosti_ras">Velikost "těla"</a>
      <input type="number" value="{$this->currentProperties->getCurrentBodySize()}"
             name="{$bodySizeInputName}"></label>
    <span class="note">přibližně <a href="https://pph.drdplus.info/#tabulka_vzdalenosti">(β(šířka) + β(výška))</a> / 2</span>
      {$this->getFatigue()}
  </div>
  <div class="col">
    Doba celkového ničení
      {$this->getTime()}
  </div>
</div>
HTML;
    }

    private function getFatigue(): string
    {
        $currentFatigueFromStatueLikeDestruction = $this->currentDestruction->getCurrentFatigueFromStatueLikeDestruction();
        if (!$currentFatigueFromStatueLikeDestruction) {
            return <<<HTML
<div class="error">Únava <strong>mimo rozsah</strong></div>
HTML;
        }
        return <<<HTML
<div>Únava z celkového ničení <strong>{$currentFatigueFromStatueLikeDestruction->getValue()}</strong ></div>
HTML;
    }

    private function getTime()
    {
        $timeOfStatueLikeDestruction = $this->currentDestruction->getCurrentTimeOfStatueLikeDestruction();
        if (!$timeOfStatueLikeDestruction) {
            return <<<HTML
<div class="error">Doba ničení <strong>mimo rozsah</strong></div>
HTML;
        }
        return <<<HTML
<strong>{$timeOfStatueLikeDestruction->getValue()} {$timeOfStatueLikeDestruction->getUnitCode()->translateTo('cs', $timeOfStatueLikeDestruction->getValue())}</strong>
HTML;
    }
}