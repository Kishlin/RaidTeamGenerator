<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="raid_player")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\PlayerRepository")
 */
class Player
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
     * @ORM\ManyToOne(targetEntity="PLL\UserBundle\Entity\Guild", inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guild;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\Preference", mappedBy="player", cascade={"persist", "remove"})
     */
    private $preferences;


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
     * @return Player
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
     * Constructor
     */
    public function __construct()
    {
        $this->preferences = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set guild
     *
     * @param \PLL\UserBundle\Entity\Guild $guild
     *
     * @return Player
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
     * Add preference
     *
     * @param \PLL\CoreBundle\Entity\Preference $preference
     *
     * @return Player
     */
    public function addPreference(\PLL\CoreBundle\Entity\Preference $preference)
    {
        $this->preferences[] = $preference;

        return $this;
    }

    /**
     * Remove preference
     *
     * @param \PLL\CoreBundle\Entity\Preference $preference
     */
    public function removePreference(\PLL\CoreBundle\Entity\Preference $preference)
    {
        $this->preferences->removeElement($preference);
    }

    /**
     * Get preferences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * Get preference for a specific build 
     * 
     * @param  \PLL\CoreBundle\Entity\Build $build 
     * 
     * @return \PLL\CoreBundle\Entity\Preference                           
     */
    public function getPreferenceForBuild(\PLL\CoreBundle\Entity\Build $build)
    {
        foreach($this->preferences as $preference) {
            if($preference->getBuild()->getId() === $build->getId()) {
                return $preference;
            }
        }

        return null;
    }

    /**
     * Sets preference for a specific build 
     * 
     * @param  \PLL\CoreBundle\Entity\Build $build 
     * @param  integer                      $level
     * 
     * @return \PLL\CoreBundle\Entity\Preference                           
     */
    public function setPreferenceForBuild(\PLL\CoreBundle\Entity\Build $build, $level)
    {
        foreach($this->preferences as $preference) {
            if($preference->getBuild()->getId() === $build->getId()) {
                $preference->setLevel($level);
                return $this;
            }
        }

        return $this;
    }

    /**
     * Returns the number of builds the player can play with the given minimal preference
     * 
     * @param  integer $min_pref 
     * 
     * @return integer           
     */
    public function getNumberOfPlayableBuilds($min_pref)
    {
        return 
            $this->preferences
            ->filter(
                function($e) use($min_pref) {
                    return $e->getLevel() >= $min_pref;
                }
            )
            ->count()
        ;
    }

    /**
     * Returns all the builds the player can play with the given minimal preference
     * 
     * @param  integer $min_pref 
     * 
     * @return array           
     */
    public function getPlayable($min_pref)
    {
        $builds = array();
        
        $this->preferences
            ->filter(
                function($e) use($min_pref) {
                    return $e->getLevel() >= $min_pref;
                }
            )
            ->forAll(
                function($k, $e) use(& $builds) {
                    return $builds[$k] = $e->getBuild();
                }
            )
        ;

        return $builds;
    }
}
