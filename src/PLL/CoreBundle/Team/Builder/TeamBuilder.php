<?php 

namespace PLL\CoreBundle\Team\Builder;

use PLL\CoreBundle\Team\Utils\Assignment;
use PLL\CoreBundle\Team\Utils\Team;

use PLL\CoreBundle\Entity\CompositionGroupBuild;
use PLL\CoreBundle\Entity\CompositionGroup;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Event;
use PLL\CoreBundle\Entity\Build;
use PLL\UserBundle\Entity\Guild;

class TeamBuilder
{
	private $players 	  = array();
	private $compositions = array();

	public function setPlayers($players)
	{
		$this->players = $players;

		return $this;
	}

	public function setCompositions($compositions)
	{
		$this->compositions = $compositions;

		return $this;
	}

	public function build($options = array())
	{
		return 'yoyo';
	}
}

 ?>