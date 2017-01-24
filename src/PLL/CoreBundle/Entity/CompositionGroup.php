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
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Composition", inversedBy="groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $composition;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\CompositionGroupBuild", mappedBy="group", cascade={"persist", "remove"})
     */
    private $builds;

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
     * Set position
     *
     * @param integer $position
     *
     * @return CompositionGroup
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set composition
     *
     * @param \PLL\UserBundle\Entity\Composition $composition
     *
     * @return CompositionGroup
     */
    public function setComposition(\PLL\UserBundle\Entity\Composition $composition)
    {
        $this->composition = $composition;

        return $this;
    }

    /**
     * Get composition
     *
     * @return \PLL\UserBundle\Entity\Composition
     */
    public function getComposition()
    {
        return $this->composition;
    }

    /**
     * Add build
     *
     * @param \PLL\CoreBundle\Entity\CompositionGroupBuild $build
     *
     * @return CompositionGroup
     */
    public function addBuild(\PLL\CoreBundle\Entity\CompositionGroupBuild $build)
    {
        $this->builds[] = $build;

        return $this;
    }

    /**
     * Remove build
     *
     * @param \PLL\CoreBundle\Entity\CompositionGroupBuild $build
     */
    public function removeBuild(\PLL\CoreBundle\Entity\CompositionGroupBuild $build)
    {
        $this->builds->removeElement($build);
    }

    /**
     * Get builds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuilds()
    {
        return $this->builds;
    }
}
