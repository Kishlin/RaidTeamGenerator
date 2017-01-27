<?php

namespace PLL\CoreBundle\Team\Utils;

use PLL\CoreBundle\Team\Exception\AlreadyAssignedException;
use PLL\CoreBundle\Team\Exception\CouldNotAssignException;
use PLL\CoreBundle\Team\Exception\NoFreeSpotException;

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

	/**
	 * Constructor
	 * 
	 * @param Composition $composition
	 */
	public function __construct(Composition $composition)
	{
		$this->composition = $composition;
		$this->assignments = array();

		foreach ($composition->getCompositionbuilds() as $key => $value) {
			$this->assignments[$key] = new Assignment();
		}
	}

	/**
	 * Assigns the player to the specified build in the team
	 * 
	 * @param  Player $player 
	 * @param  Build  $build  
	 * 
	 * @return Team           this
	 */
	public function assign(Player $player, Build $build)
	{
		if($this->getSpotsLeft($build) === 0) {
			throw new NoFreeSpotException();
		}

		if($this->isAssigned($player)) {
			throw new AlreadyAssignedException();
		}

		$keys = 
			$this->composition
			->getCompositionbuildsWithBuild($build)
			->getKeys()
		;

		foreach ($keys as $key)
		{
			if(!$this->assignments[$key]->isAssigned()) {
				$this->assignments[$key]->assign($player);
				return $this;
			}
		}

		throw new CouldNotAssignException();
	}

	/**
	 * Return the size of the team's composition
	 * 
	 * @return integer 
	 */
	public function getSize()
	{
		return $this->composition->getSize();
	}

	/**
	 * Returns whether the team has a player assigned to every build
	 * 
	 * @return boolean 
	 */
	public function isComplete()
	{
		return 
			count(
				array_filter($this->assignments, function($e) {
					return !$e->isAssigned();
				})
			) 
			=== 0
		;
	}

	/**
	 * Checks whether the player already has an assignment in the team
	 * 
	 * @param  Player  $player 
	 * 
	 * @return boolean         
	 */
	public function isAssigned(Player $player)
	{
		return
			count(
				array_filter($this->assignments, function($e) use($player) {
					return $e->getPlayer() === $player;
				})
			)
			=== 1
		;
	}

	/**
	 * Returns the total number of spots for a given build
	 * 
	 * @param  Build    $build 
	 * 
	 * @return integer        
	 */
	public function getSpotsTotal(Build $build)
	{
		return
			$this->composition
			->getCompositionbuildsWithBuild($build)
			->count()
		;
	}

	/**
	 * Returns the number of free spots for a given build
	 * 
	 * @param  Build    $build 
	 * 
	 * @return integer        
	 */
	public function getSpotsLeft(Build $build)
	{
		$spots = 0;

		$keys = 
			$this->composition
			->getCompositionbuildsWithBuild($build)
			->getKeys()
		;

		foreach ($keys as $key) {
			if(!$this->assignments[$key]->isAssigned()) {
				$spots++;
			}	
		}
		return $spots;
	}

	/**
	 * Returns the player assigned at a given position
	 * 
	 * @param  integer $position
	 *  
	 * @return Player           
	 */
	public function getPlayerAssigned($position)
	{
		if($position >= $this->getSize()) {
			throw new \OutOfBoundsException();
		}

		return $this->assignments[$position]->getPlayer();
	}

}
