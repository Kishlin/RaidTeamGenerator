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

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\Event", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\Composition", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $compositions;

    public function __construct()
    {
        parent::__construct();
        $this->builds       = new \Doctrine\Common\Collections\ArrayCollection();
        $this->players      = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events       = new \Doctrine\Common\Collections\ArrayCollection();
        $this->compositions = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Add composition
     *
     * @param \PLL\CoreBundle\Entity\Composition $composition
     *
     * @return Guild
     */
    public function addComposition(\PLL\CoreBundle\Entity\Composition $composition)
    {
        $this->compositions[] = $composition;
        $composition->setGuild($this);

        return $this;
    }

    /**
     * Remove composition
     *
     * @param \PLL\CoreBundle\Entity\Composition $composition
     */
    public function removeComposition(\PLL\CoreBundle\Entity\Composition $composition)
    {
        $this->compositions->removeElement($composition);
    }

    /**
     * Get compositions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompositions()
    {
        return $this->compositions;
    }

    /**
     * Add event
     *
     * @param \PLL\CoreBundle\Entity\Event $event
     *
     * @return Guild
     */
    public function addEvent(\PLL\CoreBundle\Entity\Event $event)
    {
        $this->events[] = $event;
        $event->setGuild($this);

        return $this;
    }

    /**
     * Remove event
     *
     * @param \PLL\CoreBundle\Entity\Event $event
     */
    public function removeEvent(\PLL\CoreBundle\Entity\Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }
}
