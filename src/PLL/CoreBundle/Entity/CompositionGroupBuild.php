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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\CompositionGroup", inversedBy="builds")
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CompositionGroupBuild
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set group
     *
     * @param \PLL\UserBundle\Entity\CompositionGroup $group
     *
     * @return CompositionGroupBuild
     */
    public function setGroup(\PLL\UserBundle\Entity\CompositionGroup $group)
    {
        $this->group = $group;
        $group->addBuild($this);

        return $this;
    }

    /**
     * Get group
     *
     * @return \PLL\UserBundle\Entity\CompositionGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set build
     *
     * @param \PLL\UserBundle\Entity\Build $build
     *
     * @return CompositionGroupBuild
     */
    public function setBuild(\PLL\UserBundle\Entity\Build $build)
    {
        $this->build = $build;
        $build->addGroup($this);

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
