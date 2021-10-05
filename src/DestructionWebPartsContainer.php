<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator;

use DrdPlus\Armourer\Armourer;
use DrdPlus\AttackSkeleton\ArmamentsUsabilityMessages;
use DrdPlus\AttackSkeleton\CurrentArmaments;
use DrdPlus\AttackSkeleton\CurrentArmamentsValues;
use DrdPlus\AttackSkeleton\CustomArmamentsState;
use DrdPlus\AttackSkeleton\HtmlHelper;
use DrdPlus\AttackSkeleton\PossibleArmaments;
use DrdPlus\AttackSkeleton\Web\AttackWebPartsContainer;
use DrdPlus\Codes\Environment\MaterialCode;
use DrdPlus\Codes\Units\SquareUnitCode;
use DrdPlus\Codes\Units\VolumeUnitCode;
use DrdPlus\DestructionCalculator\Web\BasicItemDestructionBody;
use DrdPlus\DestructionCalculator\Web\BodyLikeBody;
use DrdPlus\DestructionCalculator\Web\DamageBody;
use DrdPlus\DestructionCalculator\Web\MaterialBody;
use DrdPlus\DestructionCalculator\Web\VoluminousItemBody;
use DrdPlus\RulesSkeleton\Configurations\Configuration;
use DrdPlus\RulesSkeleton\Configurations\Dirs;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\UsagePolicy;
use DrdPlus\RulesSkeleton\Web\Tools\WebFiles;

class DestructionWebPartsContainer extends AttackWebPartsContainer
{
    private \DrdPlus\DestructionCalculator\CurrentDestruction $currentDestruction;
    /**
     * @var CurrentProperties
     */
    private $currentProperties;
    private ?\DrdPlus\DestructionCalculator\Web\BasicItemDestructionBody $basicItemDestructionBody = null;
    private ?\DrdPlus\DestructionCalculator\Web\BodyLikeBody $bodyLikeBody = null;
    private ?\DrdPlus\DestructionCalculator\Web\DamageBody $damageBody = null;
    private ?\DrdPlus\DestructionCalculator\Web\MaterialBody $materialBody = null;
    private ?\DrdPlus\DestructionCalculator\Web\VoluminousItemBody $voluminousItemBody = null;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

    public function __construct(
        Configuration $configuration,
        UsagePolicy $usagePolicy,
        WebFiles $webFiles,
        Dirs $dirs,
        HtmlHelper $htmlHelper,
        Request $request,
        CurrentProperties $currentProperties,
        CustomArmamentsState $customArmamentsState,
        CurrentArmamentsValues $currentArmamentsValues,
        CurrentArmaments $currentArmaments,
        PossibleArmaments $possibleArmaments,
        ArmamentsUsabilityMessages $armamentsUsabilityMessages,
        Armourer $armourer,
        CurrentDestruction $currentDestruction
    )
    {
        parent::__construct(
            $configuration,
            $usagePolicy,
            $webFiles,
            $dirs,
            $htmlHelper,
            $request,
            $currentProperties,
            $customArmamentsState,
            $currentArmamentsValues,
            $currentArmaments,
            $possibleArmaments,
            $armamentsUsabilityMessages,
            $armourer
        );
        $this->currentProperties = $currentProperties;
        $this->currentDestruction = $currentDestruction;
        $this->htmlHelper = $htmlHelper;
    }

    /**
     * @return array|MaterialCode[]
     */
    public function getMaterialCodes(): array
    {
        return array_map(
            static fn(string $materialValue) => MaterialCode::getIt($materialValue),
            MaterialCode::getPossibleValues()
        );
    }

    /**
     * @return array|VolumeUnitCode[]
     */
    public function getVolumeUnits(): array
    {
        return array_map(
            static fn(string $volumeUnitValue) => VolumeUnitCode::getIt($volumeUnitValue),
            VolumeUnitCode::getPossibleValues()
        );
    }

    /**
     * @return array|SquareUnitCode[]
     */
    public function getSquareUnits(): array
    {
        return array_map(
            static fn(string $squareUnitValue) => SquareUnitCode::getIt($squareUnitValue),
            SquareUnitCode::getPossibleValues()
        );
    }

    public function getCurrentDestruction(): CurrentDestruction
    {
        return $this->currentDestruction;
    }

    public function getCurrentProperties(): CurrentProperties
    {
        return $this->currentProperties;
    }

    public function getBasicItemDestructionBody(): BasicItemDestructionBody
    {
        if ($this->basicItemDestructionBody === null) {
            $this->basicItemDestructionBody = new BasicItemDestructionBody($this);
        }
        return $this->basicItemDestructionBody;
    }

    public function getBodyLikeBody(): BodyLikeBody
    {
        if ($this->bodyLikeBody === null) {
            $this->bodyLikeBody = new BodyLikeBody($this);
        }
        return $this->bodyLikeBody;
    }

    public function getDamageBody(): DamageBody
    {
        if ($this->damageBody === null) {
            $this->damageBody = new DamageBody($this, $this->htmlHelper);
        }
        return $this->damageBody;
    }

    public function getMaterialBody(): MaterialBody
    {
        if ($this->materialBody === null) {
            $this->materialBody = new MaterialBody($this, $this->htmlHelper);
        }
        return $this->materialBody;
    }

    public function getVoluminousItemBody(): VoluminousItemBody
    {
        if ($this->voluminousItemBody === null) {
            $this->voluminousItemBody = new VoluminousItemBody($this, $this->htmlHelper);
        }
        return $this->voluminousItemBody;
    }
}
