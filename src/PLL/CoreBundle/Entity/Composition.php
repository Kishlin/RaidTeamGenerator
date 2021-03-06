<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Composition
 *
 * @ORM\Table(name="raid_composition")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\CompositionRepository")
 */
class Composition
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="boss", type="string", length=255)
     */
    private $boss;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\UserBundle\Entity\Guild", inversedBy="compositions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guild;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\CompositionBuild", mappedBy="composition", cascade={"persist", "remove"})
     */
    private $compositionbuilds;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var int
     *
     * @ORM\Column(name="groupcount", type="integer")
     */
    private $groupscount;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->compositionbuilds = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Composition
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set boss
     *
     * @param string $boss
     *
     * @return Composition
     */
    public function setBoss($boss)
    {
        $this->boss = $boss;

        return $this;
    }

    /**
     * Get boss
     *
     * @return string
     */
    public function getBoss()
    {
        return $this->boss;
    }

    /**
     * Set guild
     *
     * @param \PLL\UserBundle\Entity\Guild $guild
     *
     * @return Composition
     */
    public function setGuild(\PLL\UserBundle\Entity\Guild $guild)
    {
        $this->guild = $guild;

        return $this;
    }

    /**
     * Get guild
     *
     * @return \PLL\UserBundle\Entity\Guild
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Composition
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set groupscount
     *
     * @param integer $groupscount
     *
     * @return Composition
     */
    public function setGroupscount($groupscount)
    {
        $this->groupscount = $groupscount;

        return $this;
    }

    /**
     * Get groupscount
     *
     * @return integer
     */
    public function getGroupscount()
    {
        return $this->groupscount;
    }

    /**
     * Add build
     *
     * @param \PLL\CoreBundle\Entity\CompositionBuild $compositionbuild
     *
     * @return Composition
     */
    public function addCompositionbuild(\PLL\CoreBundle\Entity\CompositionBuild $compositionbuild)
    {
        $this->compositionbuilds[] = $compositionbuild;
        $compositionbuild->setComposition($this);

        return $this;
    }

    /**
     * Remove build
     *
     * @param \PLL\CoreBundle\Entity\CompositionBuild $compositionbuild
     */
    public function removeCompositionbuild(\PLL\CoreBundle\Entity\CompositionBuild $compositionbuild)
    {
        $this->compositionbuilds->removeElement($compositionbuild);
    }

    /**
     * Get compositionbuilds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompositionbuilds()
    {
        return $this->compositionbuilds;
    }

    /**
     * Returns every compositionbuild with a specific builc
     * 
     * @param  Build $build 
     * 
     * @return ArrayCollection        
     */
    public function getCompositionbuildsWithBuild($build) 
    {
        return $this
            ->compositionbuilds
            ->filter(
                function($e) use($build) {
                    return $e->getBuild()->getId() === $build->getId();
                }
            )
        ;
    }

    /**
     * Returns an array containing once every guild appearing in the composition
     * 
     * @return array
     */
    public function getAllBuilds()
    {
        $builds = array();
        
        $this
            ->compositionbuilds
            ->forAll(
                function($k, $e) use(& $builds) {
                    if(!in_array($e->getBuild(), $builds)) {
                        return $builds[] = $e->getBuild();
                    } else {
                        return $builds;
                    }
                }
            )
        ;
        
        return $builds;
    }

    /**
     * Returns the compositionbuilds for a specific group
     * 
     * @param  integer         $groupindex 
     * 
     * @return ArrayCollection             
     */
    public function getGroup($groupindex)
    {
        return $this
            ->compositionbuilds
            ->filter(function($e) use($groupindex) {
                return $e->getGroupindex() === $groupindex;
            })
        ;
    }

    /**
     * Returns the size of the specific group
     * 
     * @param  integer $groupindex 
     * 
     * @return integer             
     */
    public function getGroupSize($groupindex)
    {
        return $this
            ->compositionbuilds
            ->filter(function($e) use($groupindex) {
                return $e->getGroupindex() === $groupindex;
            })
            ->count()
        ;
    }

    /**
     * Returns all the builds for a specific group
     * 
     * @param  integer $groupindex
     *  
     * @return array             
     */
    public function getBuildsForGroup($groupindex)
    {
        $builds = array();
        
        $this
            ->getGroup($groupindex)
            ->forAll(
                function($k, $e) use(& $builds) {
                    return $builds[$k] = $e->getBuild();
                }
            )
        ;
        
        return $builds;
    }

    /**
     * Returns the build for the given position
     * 
     * @param  integer $position 
     * 
     * @return            
     */
    public function getBuild($position)
    {   
        if($position >= $this->size) {
            throw new \OutOfBoundsException();
        }

        return $this
            ->compositionbuilds
            ->get($position)
            ->getBuild()
        ;
    }

    public function toString()
    {
        return 
            $this->name . ' |' .
            join(', ', $this->compositionbuilds->map(
                function($value) {
                    return $value->getGroupindex() . ': ' .$value->getBuild()->getName();
                }
            )->toArray())
        ;
    }
}
