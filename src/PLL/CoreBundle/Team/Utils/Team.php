<?php

namespace PLL\CoreBundle\Team\Utils;


use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Build;

/**
 * Team
 */
class Team
{
	/**
	 * @var PLL\CoreBundle\Entity\Composition
	 */
	private $composition;

	/**
	 * @var array
	 */
	private $assignments;

	public function __construct(Composition $composition)
	{
		$this->composition = $composition;
		$this->assignments = array();

		for ($i=0, $len = $composition->getSize(); $i < $len; $i++) { 
			$this->assignments[$i] = new Assignment();
		}
	}

	public function assign(Player $player, Build $build)
	{
		
	}

}
