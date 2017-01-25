<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Build
 *
 * @ORM\Table(name="raid_build")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\BuildRepository")
 */
class Build
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
     * @ORM\Column(name="img", type="string", length=255)
     */
    private $img;

    /**
     * @var string
     *
     * @ORM\Column(name="imgsub", type="string", length=255, nullable=true)
     */
    private $imgsub;

    /**
     * @ORM\ManyToOne(targetEntity="PLL\UserBundle\Entity\Guild", inversedBy="builds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guild;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\Preference", mappedBy="build", cascade={"persist", "remove"})
     */
    private $preferences;

    /**
     * @ORM\OneToMany(targetEntity="PLL\CoreBundle\Entity\CompositionBuild", mappedBy="build", cascade={"persist", "remove"})
     */
    private $compositionbuilds;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->preferences       = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Build
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
     * Set img
     *
     * @param string $img
     *
     * @return Build
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set imgsub
     *
     * @param string $imgsub
     *
     * @return Build
     */
    public function setImgsub($imgsub)
    {
        $this->imgsub = $imgsub;

        return $this;
    }

    /**
     * Get imgsub
     *
     * @return string
     */
    public function getImgsub()
    {
        return $this->imgsub;
    }

    /**
     * Set guild
     *
     * @param \PLL\UserBundle\Entity\Guild $guild
     *
     * @return Build
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
     * @return Build
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
     * Get preference for a specific player 
     * 
     * @param  \PLL\CoreBundle\Entity\Player $player 
     * 
     * @return integer                          
     */
    public function getPreferenceForPlayer(\PLL\CoreBundle\Entity\Player $player)
    {
        foreach($this->preferences as $preference) {
            if($preference->getPlayer() === $player) {
                return $preference;
            }
        }

        return null;
    }

    /**
     * Add compositionbuild
     *
     * @param \PLL\CoreBundle\Entity\CompositionBuild $compositionbuild
     *
     * @return Build
     */
    public function addCompositionbuild(\PLL\CoreBundle\Entity\CompositionBuild $compositionbuild)
    {
        $this->compositionbuilds[] = $compositionbuild;

        return $this;
    }

    /**
     * Remove compositionbuild
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
}
