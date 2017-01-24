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
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\CompositionGroup", mappedBy="composition")
     */
    private $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add group
     *
     * @param \PLL\UserBundle\Entity\CompositionGroup $group
     *
     * @return Composition
     */
    public function addGroup(\PLL\UserBundle\Entity\CompositionGroup $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \PLL\UserBundle\Entity\CompositionGroup $group
     */
    public function removeGroup(\PLL\UserBundle\Entity\CompositionGroup $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
