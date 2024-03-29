<?php declare(strict_types=1);

namespace DrdPlus\DestructionCalculator;

use DrdPlus\AttackSkeleton\AttackServicesContainer;
use DrdPlus\Destruction\Destruction;
use DrdPlus\RulesSkeleton\Web\Tools\WebFiles;
use DrdPlus\RulesSkeleton\Web\Tools\WebPartsContainer;

class DestructionServiceContainer extends AttackServicesContainer
{
    private ?\DrdPlus\Destruction\Destruction $destruction = null;
    private ?\DrdPlus\DestructionCalculator\CurrentDestruction $currentDestruction = null;
    /**
     * @var CurrentProperties
     */
    private $currentProperties;
    private ?\DrdPlus\DestructionCalculator\DestructionWebPartsContainer $routedDestructionWebPartsContainer = null;
    private ?\DrdPlus\DestructionCalculator\DestructionWebPartsContainer $rootDestructionWebPartsContainer = null;

    public function getRoutedWebPartsContainer(): WebPartsContainer
    {
        if ($this->routedDestructionWebPartsContainer === null) {
            $this->routedDestructionWebPartsContainer = $this->createDestructionWebPartsContainer($this->getRoutedWebFiles());
        }
        return $this->routedDestructionWebPartsContainer;
    }

    private function createDestructionWebPartsContainer(WebFiles $webFiles): DestructionWebPartsContainer
    {
        return new DestructionWebPartsContainer(
            $this->getConfiguration(),
            $this->getUsagePolicy(),
            $webFiles,
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

    public function getRootWebPartsContainer(): WebPartsContainer
    {
        if ($this->rootDestructionWebPartsContainer === null) {
            $this->rootDestructionWebPartsContainer = $this->createDestructionWebPartsContainer($this->getRootWebFiles());
        }
        return $this->rootDestructionWebPartsContainer;
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
