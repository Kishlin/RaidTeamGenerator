<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="raid_event")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\EventRepository")
 */
class Event
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \string
     *
     * @ORM\Column(name="time", type="string", length=255)
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\UserBundle\Entity\Guild", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guild;

    /**
     * @ORM\ManyToMany(targetEntity="PLL\CoreBundle\Entity\Composition", cascade={"persist"})
     * @ORM\JoinTable(name="raid_event_compositions")
     */
    private $compositions;

    /**
     * @ORM\ManyToMany(targetEntity="PLL\CoreBundle\Entity\Player", cascade={"persist"})
     * @ORM\JoinTable(name="raid_event_players")
     */
    private $players;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->compositions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Event
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Event
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set guild
     *
     * @param \PLL\UserBundle\Entity\Guild $guild
     *
     * @return Event
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
     * Add composition
     *
     * @param \PLL\CoreBundle\Entity\Composition $composition
     *
     * @return Event
     */
    public function addComposition(\PLL\CoreBundle\Entity\Composition $composition)
    {
        $this->compositions[] = $composition;

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
     * Add player
     *
     * @param \PLL\CoreBundle\Entity\Player $player
     *
     * @return Event
     */
    public function addPlayer(\PLL\CoreBundle\Entity\Player $player)
    {
        $this->players[] = $player;

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
