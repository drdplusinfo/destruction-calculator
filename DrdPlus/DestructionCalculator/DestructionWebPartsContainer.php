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
use DrdPlus\RulesSkeleton\Dirs;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\Web\Pass;
use DrdPlus\RulesSkeleton\Web\WebFiles;

class DestructionWebPartsContainer extends AttackWebPartsContainer
{
    /**
     * @var CurrentDestruction
     */
    private $currentDestruction;
    /**
     * @var CurrentProperties
     */
    private $currentProperties;
    /**
     * @var BasicItemDestructionBody
     */
    private $basicItemDestructionBody;
    /**
     * @var BodyLikeBody
     */
    private $bodyLikeBody;
    /**
     * @var DamageBody
     */
    private $damageBody;
    /**
     * @var MaterialBody
     */
    private $materialBody;
    /**
     * @var VoluminousItemBody
     */
    private $voluminousItemBody;
    /**
     * @var HtmlHelper
     */
    private $htmlHelper;

    public function __construct(
        Pass $pass,
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
            $pass,
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
            static function (string $materialValue) {
                return MaterialCode::getIt($materialValue);
            },
            MaterialCode::getPossibleValues()
        );
    }

    /**
     * @return array|VolumeUnitCode[]
     */
    public function getVolumeUnits(): array
    {
        return array_map(
            static function (string $volumeUnitValue) {
                return VolumeUnitCode::getIt($volumeUnitValue);
            },
            VolumeUnitCode::getPossibleValues()
        );
    }

    /**
     * @return array|SquareUnitCode[]
     */
    public function getSquareUnits(): array
    {
        return array_map(
            static function (string $squareUnitValue) {
                return SquareUnitCode::getIt($squareUnitValue);
            },
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