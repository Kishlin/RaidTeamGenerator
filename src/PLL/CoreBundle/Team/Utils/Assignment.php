<?php

namespace PLL\CoreBundle\Team\Utils;

use PLL\CoreBundle\Entity\Player;

/**
 * Assignment
 */
class Assignment
{
	/**
	 * @var \PLL\CoreBundle\Entity\Player
	 */
	private $player;

	/**
	 * @var boolean
	 */
	private $assigned;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->player   = null;
		$this->assigned = false;
	}

	/**
	 * Assign a player
	 * 
	 * @param  Player     $player 
	 * @return Assignment this
	 */
	public function assign(Player $player)
	{
		$this->assigned = true;
		$this->player   = $player;

		return $this;
	}

	/**
	 * Undo the assignment
	 * 
	 * @return Assignment this
	 */
	public function unassign()
	{
		$this->player   = null;
		$this->assigned = false;

		return $this;
	}

	/**
	 * Returns wether the assignment was made.
	 * 
	 * @return boolean assigned
	 */
	public function isAssigned()
	{
		return $this->assigned;
	}

	/**
	 * Returns the assigned player.
	 * 
	 * @return \PLL\CoreBundle\Entity\Player player
	 */
	public function getPlayer()
	{
		return $this->player;
	}
}
