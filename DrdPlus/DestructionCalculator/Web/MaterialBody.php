<?php
declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class MaterialBody extends AbstractDestructionBody
{
    /**
     * @var CurrentDestruction
     */
    private $currentDestruction;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

    /**
     * @var array|MaterialCode[]
     */
    private $materialCodes;

    public function __construct(DestructionWebPartsContainer $destructionWebPartsContainer, HtmlHelper $htmlHelper)
    {
        $this->materialCodes = $destructionWebPartsContainer->getMaterialCodes();
        $this->currentDestruction = $destructionWebPartsContainer->getCurrentDestruction();
        $this->htmlHelper = $htmlHelper;
    }

    public function getValue(): string
    {
        return <<<HTML
<div class="row">
  <div class="col">
    <label>Materiál
      <select name="material">{$this->getMaterialOptions()}</select>          
    </label>
  </div>
  <div class="col">
    {$this->getFailureInfo()}
  </div>
</div>
HTML;
    }

    private function getMaterialOptions(): string
    {
        $materialOptions = [];
        $currentMaterial = $this->currentDestruction->getCurrentMaterial();
        foreach ($this->materialCodes as $materialCode) {
            $selected = $this->htmlHelper->getSelected($materialCode, $currentMaterial);
            $materialOptions[] = <<<HTML
<option value="{$materialCode}" {$selected}>{$materialCode->translateTo('cs')}
      (pevnost {$this->currentDestruction->getMaterialResistance($materialCode)})
</option>
HTML;
        }
        return \implode("\n", $materialOptions);
    }

    private function getFailureInfo(): string
    {
        if ($this->currentDestruction->getCurrentRollOnDestruction()->isSuccess()) {
            return '';
        }
        return <<<HTML
<div class="alert alert-danger">Předmět <strong>nebyl</strong> poškozen</div>
HTML;
    }
}