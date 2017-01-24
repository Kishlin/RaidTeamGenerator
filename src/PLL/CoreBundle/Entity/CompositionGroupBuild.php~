<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompositionGroupBuild
 *
 * @ORM\Table(name="raid_composition_group_build")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\CompositionGroupBuildRepository")
 */
class CompositionGroupBuild
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\CompositionGroup", inversedBy="groupbuilds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Build", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $build;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set group
     *
     * @param \PLL\CoreBundle\Entity\CompositionGroup $group
     *
     * @return CompositionGroupBuild
     */
    public function setGroup(\PLL\CoreBundle\Entity\CompositionGroup $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \PLL\CoreBundle\Entity\CompositionGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set build
     *
     * @param \PLL\CoreBundle\Entity\Build $build
     *
     * @return CompositionGroupBuild
     */
    public function setBuild(\PLL\CoreBundle\Entity\Build $build)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return \PLL\UserBundle\Entity\Build
     */
    public function getBuild()
    {
        return $this->build;
    }
}
