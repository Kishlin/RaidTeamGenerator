<?php

namespace PLL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiKey
 *
 * @ORM\Table(name="raid_api_key")
 * @ORM\Entity(repositoryClass="PLL\CoreBundle\Repository\ApiKeyRepository")
 */
class ApiKey
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
     * @ORM\Column(name="apikey", type="string", length=255)
     */
    private $apikey;

    /**
     * @ORM\OneToOne(targetEntity="PLL\UserBundle\Entity\Guild", mappedBy="apikey")
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
     * Set apikey
     *
     * @param string $apikey
     *
     * @return ApiKey
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }

    /**
     * Get apikey
     *
     * @return string
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * Set guild
     *
     * @param \PLL\UserBundle\Entity\Guild $guild
     *
     * @return ApiKey
     */
    public function setGuild(\PLL\UserBundle\Entity\Guild $guild = null)
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
