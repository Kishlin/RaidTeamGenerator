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
}
