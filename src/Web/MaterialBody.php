<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator\Web;

use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\DestructionCalculator\CurrentDestruction;
use DrdPlus\DestructionCalculator\DestructionWebPartsContainer;

class MaterialBody extends AbstractDestructionBody
{
    private \DrdPlus\DestructionCalculator\CurrentDestruction $currentDestruction;
    private \DrdPlus\AttackSkeleton\HtmlHelper $htmlHelper;

    /**
     * @var array|MaterialCode[]
     */
    private array $materialCodes;

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
        return implode("\n", $materialOptions);
    }
}