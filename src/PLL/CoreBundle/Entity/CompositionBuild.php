<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompositionBuild
 *
 * @ORM\Table(name="raid_composition_build")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\CompositionBuildRepository")
 */
class CompositionBuild
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
     * @ORM\Column(name="groupindex", type="integer")
     */
    private $groupindex;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Composition", inversedBy="compositionbuilds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $composition;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Build")
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
     * Set groupindex
     *
     * @param integer $groupindex
     *
     * @return CompositionBuild
     */
    public function setGroupindex($groupindex)
    {
        $this->groupindex = $groupindex;

        return $this;
    }

    /**
     * Get groupindex
     *
     * @return int
     */
    public function getGroupindex()
    {
        return $this->groupindex;
    }

    /**
     * Set composition
     *
     * @param \PLL\CoreBundle\Entity\Composition $composition
     *
     * @return CompositionBuild
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
     * Set build
     *
     * @param \PLL\CoreBundle\Entity\Build $build
     *
     * @return CompositionBuild
     */
    public function setBuild(\PLL\CoreBundle\Entity\Build $build)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return \PLL\CoreBundle\Entity\Build
     */
    public function getBuild()
    {
        return $this->build;
    }
}
