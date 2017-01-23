<?php

namespace PLL\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="raid_guild")
 * @ORM\Entity(repositoryClass="PLL\UserBundle\Repository\GuildRepository")
 */
class Guild extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\Build", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $builds;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\Player", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $players;

    public function __construct()
    {
        parent::__construct();
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add build
     *
     * @param \PLL\CoreBundle\Entity\Build $build
     *
     * @return Guild
     */
    public function addBuild(\PLL\CoreBundle\Entity\Build $build)
    {
        $this->builds[] = $build;
        $build->setGuild($this);

        return $this;
    }

    /**
     * Remove build
     *
     * @param \PLL\CoreBundle\Entity\Build $build
     */
    public function removeBuild(\PLL\CoreBundle\Entity\Build $build)
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

    /**
     * Add player
     *
     * @param \PLL\CoreBundle\Entity\Player $player
     *
     * @return Guild
     */
    public function addPlayer(\PLL\CoreBundle\Entity\Player $player)
    {
        $this->players[] = $player;
        $player->setGuild($this);

        return $this;
    }

    /**
     * Remove player
     *
     * @param \PLL\CoreBundle\Entity\Player $player
     */
    public function removePlayer(\PLL\CoreBundle\Entity\Player $player)
    {
        $this->players->removeElement($player);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }
}
