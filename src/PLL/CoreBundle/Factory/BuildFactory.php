<?php

namespace PLL\CoreBundle\Factory;

use PLL\CoreBundle\Entity\Build;

/**
 * Build Factory
 */
class BuildFactory
{
    public function createBuild($name, $main, $sub) 
    {
        $build = new Build();
        $build->setName($name);
        $build->setImg($main);
        $build->setImgsub($sub);
        return $build;
    }
}
