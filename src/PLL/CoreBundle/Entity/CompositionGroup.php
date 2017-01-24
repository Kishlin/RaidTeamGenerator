<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompositionGroup
 *
 * @ORM\Table(name="raid_composition_group")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\CompositionGroupRepository")
 */
class CompositionGroup
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
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Composition", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $composition;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\CompositionGroupBuild", mappedBy="group", cascade={"persist", "remove"})
     */
    private $groupbuilds;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set composition
     *
     * @param \PLL\CoreBundle\Entity\Composition $composition
     *
     * @return CompositionGroup
     */
    public function setComposition(\PLL\CoreBundle\Entity\Composition $composition)
    {
        $this->composition = $composition;

        return $this;
    }

    /**
     * Get composition
     *
     * @return \PLL\CoreBundle\Entity\Composition
     */
    public function getComposition()
    {
        return $this->composition;
    }

    /**
     * Add groupbuild
     *
     * @param \PLL\CoreBundle\Entity\CompositionGroupBuild $groupbuild
     *
     * @return CompositionGroup
     */
    public function addGroupbuild(\PLL\CoreBundle\Entity\CompositionGroupBuild $groupbuild)
    {
        $this->groupbuilds[] = $groupbuild;
        $groupbuild->setGroup($this);

        return $this;
    }

    /**
     * Remove groupbuild
     *
     * @param \PLL\CoreBundle\Entity\CompositionGroupBuild $groupbuild
     */
    public function removeGroupbuild(\PLL\CoreBundle\Entity\CompositionGroupBuild $groupbuild)
    {
        $this->groupbuilds->removeElement($groupbuild);
    }

    /**
     * Get groupbuilds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupbuilds()
    {
        return $this->groupbuilds;
    }

    /**
     * Adds a build to the group.
     * 
     * @param PLLCoreBundleEntityBuild $groupbuild [description]
     */
    public function addBuild(\PLL\CoreBundle\Entity\Build $build) 
    {
        $groupbuild = new \PLL\CoreBundle\Entity\CompositionGroupBuild();
        $build->addGroup($groupbuild);
        $this->addGroupbuild($groupbuild);

        return $this;
    }
}
