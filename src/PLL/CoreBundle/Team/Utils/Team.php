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
	 * Returns the number of groups in the composition
	 * 
	 * @return integer 
	 */
	public function getGroupCount()
	{
		return $this->composition->getGroupscount();
	}

	public function getAssignmentsForGroup($group)
	{
		$assignments = array();

		$group = $this->composition->getGroup($group);
		$keys = $group->getKeys();

		foreach ($keys as $key)
		{
			$assignments[] = array(
				'build'  => $group->get($key)->getBuild(),
				'player' => $this->getPlayerAssigned($key),
			);
		}

		usort($assignments, function($a, $b) {
			return $a['build']->getId() - $b['build']->getId();
		});

		return $assignments;
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
	 * Tries filling the remaining blank spots
	 *
	 * @param  array   $players
	 * 
	 * @return boolean
	 */
	public function attemptFilling($players, $logger)
	{
		$logger->debug("Trying to fill : " . $this->toString());

		$lonely_players = $free_spots = array();

		foreach ($players as $player) {
			if(!$this->isAssigned($player)) {
				$lonely_players[] = $player;
			}
		}

		foreach ($this->assignments as $key => $value) {
			if($value->getPlayer() === null) {
				$free_spots[$key] = $value;
			}
		}

		foreach ($lonely_players as $player) {
			foreach ($this->assignments as $key => $value) {
				if($value->getPlayer() !== null) {
					$origin = $value;
					$origin_player = $value->getPlayer();
					$origin_build = $this->composition->getCompositionbuilds()->get($key)->getBuild();
					if(in_array($origin_build, $player->getPlayable(1))) {
						foreach ($free_spots as $key2 => $value2) {
							$target = $value2;
							$target_assignment = $value2;
							$target_build = $this->composition->getCompositionbuilds()->get($key2)->getBuild();
							if($origin_player->getPreferenceForBuild($target_build)->getLevel() > 0) {
								$origin->unassign();
								$this->assign($origin_player, $target_build);
								$this->assign($player, $origin_build);
								return $this->attemptFilling($players, $logger);
							}
						}
					}
				}
			}
		}

		$logger->debug($this->toString());

		return $this->isComplete();
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
					if($e->getPlayer() === null) {
						return false;
					} else {
						return $e->getPlayer()->getId() === $player->getId();
					}
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

	public function toString()
	{
		return $this
			->composition->toString() . ' <=> ' . 
			join(', ', array_map(
				function($value) {
					$player = $value->getPlayer();
					if($player !== null) {
						return $player->getName();
					} else {
						return "NULL";
					}
				},
				$this->assignments
			))
		;
	}


    /**
     * Gets the value of composition.
     *
     * @return PLL\CoreBundle\Entity\Composition
     */
    public function getComposition()
    {
        return $this->composition;
    }

    /**
     * Gets the value of assignments.
     *
     * @return array
     */
    public function getAssignments()
    {
        return $this->assignments;
    }
}
