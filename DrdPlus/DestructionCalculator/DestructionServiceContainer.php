<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\Destruction\Destruction;

class DestructionServiceContainer extends AttackServicesContainer
{
    /**
     * @var Destruction
     */
    private $destruction;
    /**
     * @var CurrentDestruction
     */
    private $currentDestruction;
    /**
     * @var CurrentProperties
     */
    private $currentProperties;
    /**
     * @var DestructionWebPartsContainer
     */
    private $destructionWebPartsContainer;

    public function getRoutedWebPartsContainer(): \DrdPlus\RulesSkeleton\Web\WebPartsContainer
    {
        if ($this->destructionWebPartsContainer === null) {
            $this->destructionWebPartsContainer = new DestructionWebPartsContainer(
                $this->getPass(),
                $this->getRoutedWebFiles(),
                $this->getDirs(),
                $this->getHtmlHelper(),
                $this->getRequest(),
                $this->getCurrentProperties(),
                $this->getCustomArmamentsState(),
                $this->getCurrentArmamentsValues(),
                $this->getCurrentArmaments(),
                $this->getPossibleArmaments(),
                $this->getArmamentsUsabilityMessages(),
                $this->getArmourer(),
                $this->getCurrentDestruction()
            );
        }
        return $this->destructionWebPartsContainer;
    }

    public function getDestruction(): Destruction
    {
        if ($this->destruction === null) {
            $this->destruction = new Destruction($this->getArmourer());
        }
        return $this->destruction;
    }

    public function getCurrentDestruction(): CurrentDestruction
    {
        if ($this->currentDestruction === null) {
            $this->currentDestruction = new CurrentDestruction(
                $this->getCurrentArmaments(),
                $this->getCurrentProperties(),
                $this->getCurrentValues(),
                $this->getDestruction(),
                $this->getTables()
            );
        }
        return $this->currentDestruction;
    }

    /**
     * @return \DrdPlus\AttackSkeleton\CurrentProperties|CurrentProperties
     */
    public function getCurrentProperties(): \DrdPlus\AttackSkeleton\CurrentProperties
    {
        if ($this->currentProperties === null) {
            $this->currentProperties = new CurrentProperties($this->getCurrentValues());
        }
        return $this->currentProperties;
    }
}