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

	public function __construct(Composition $composition)
	{
		$this->composition = $composition;
		$this->assignments = array();

		foreach ($composition->getCompositionbuilds() as $key => $value) {
			$this->assignments[$key] = new Assignment();
		}
	}

	public function assign(Player $player, Build $build)
	{
		if($this->getSpotsLeft($build) === 0) {
			throw new NoFreeSpotException();
		}

		if($this->isAssigned($player)) {
			throw new AlreadyAssignedException();
		}

		$keys = 
			$this->composition->getCompositionbuilds()
			->filter( 
				function($e) use($build) {
					return $e->getBuild() === $build;
				}
			)
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

	public function getSize()
	{
		return $this->composition->getSize();
	}

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

	public function getSpotsTotal(Build $build)
	{
		return
			$this->composition->getCompositionbuilds()
			->filter(
				function($e) use($build) {
					return $e->getBuild() === $build;
				}
			)
			->count()
		;
	}

	public function getSpotsLeft(Build $build)
	{
		$keys = 
			$this->composition->getCompositionbuilds()
			->filter( 
				function($e) use($build) {
					return $e->getBuild() === $build;
				}
			)
			->getKeys()
		;

		$spots = 0;
		foreach ($keys as $key) {
			if(!$this->assignments[$key]->isAssigned()) {
				$spots++;
			}	
		}
		return $spots;
	}

	public function getPlayerAssigned($position)
	{
		if($position >= $this->getSize()) {
			throw new \OutOfBoundsException();
		}

		return $this->assignments[$position]->getPlayer();
	}

}
