<?php

namespace PLL\CoreBundle\Service;

use PLL\CoreBundle\Factory\BuildFactory;
use PLL\CoreBundle\Entity\Build;

class DefaultBuilds
{
    /**
     * @var \PLL\CoreBundle\Factory\BuildFactory
     */
    private $factory;

    public function __construct(BuildFactory $factory)
    {
        $this->factory = $factory;
    }

    public function getDefaultBuilds()
    {
        $builds = array();

        $builds[] = $this->factory->createBuild("Power Berserker", "Berserker", "None");
        $builds[] = $this->factory->createBuild("Condi Berserker", "Berserker", "Burning");
        $builds[] = $this->factory->createBuild("Tempest", "Tempest", "None");
        $builds[] = $this->factory->createBuild("Dragonhunter", "Dragonhunter", "None");
        $builds[] = $this->factory->createBuild("Daredeveil", "Daredevil", "None");
        $builds[] = $this->factory->createBuild("Chronomancer", "Chronomancer", "None");
        $builds[] = $this->factory->createBuild("Condi Mesmer", "Mesmer", "Torment");
        $builds[] = $this->factory->createBuild("Power Druid", "Druid", "None");
        $builds[] = $this->factory->createBuild("Healing Druid", "Druid", "Regeneration");
        $builds[] = $this->factory->createBuild("Condi Druid", "Druid", "Burning");
        $builds[] = $this->factory->createBuild("Condi Ranger", "Ranger", "Burning");
        $builds[] = $this->factory->createBuild("Condi Scrapper", "Scrapper", "Burning");
        $builds[] = $this->factory->createBuild("Condi Reaper", "Reaper", "Bleeding");
        $builds[] = $this->factory->createBuild("Herald", "Herald", "None");

        return $builds;
    }
}