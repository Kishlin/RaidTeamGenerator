<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Preference
 *
 * @ORM\Table(name="raid_preference")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\PreferenceRepository")
 */
class Preference
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
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Player", inversedBy="preferences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\CoreBundle\Entity\Build", inversedBy="preferences")
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
     * Set level
     *
     * @param integer $level
     *
     * @return Preference
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set player
     *
     * @param \PLL\CoreBundle\Entity\Player $player
     *
     * @return Preference
     */
    public function setPlayer(\PLL\CoreBundle\Entity\Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \PLL\CoreBundle\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set build
     *
     * @param \PLL\CoreBundle\Entity\Build $build
     *
     * @return Preference
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
