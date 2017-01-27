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
	CONST CEIL_CRITICAL_BUILD = 1;
	CONST CEIL_CRITICAL_PLAYER_LEVEL = 5;
	CONST CEIL_CRITICAL_PLAYER_COUNT = 2;

	CONST FLAG_BUILD_IMPOSSIBLE = 0;
	CONST FLAG_BUILD_CRITICAL   = 1;
	CONST FLAG_BUILD_STABLE     = 2;
	CONST FLAG_BUILD_UNSTABLE   = 3;

	/**
	 * @var array
	 */
	private $teams = array();

	/**
	 * @var array
	 */
	private $compositions     = array();
	/**
	 * @var array
	 */
	private $players 	      = array();
	/**
	 * @var array
	 */
	private $critical_players = array();
	/**
	 * @var array
	 */
	private $builds 		  = array();

	/*
	 * Constructor
	 */
	public function __construct() 
	{

	}

	/**
	 * Sets the players
	 * 
	 * @param array $players
	 */
	public function setPlayers($players)
	{
		if($players instanceof \Doctrine\ORM\PersistentCollection) {
			$this->players = $players->toArray();
		} else {
			$this->players = $players;
		}

		return $this;
	}

	/**
	 * Sets the compositions
	 * 
	 * @param array $compositions 
	 */
	public function setCompositions($compositions)
	{
		if($compositions instanceof \Doctrine\ORM\PersistentCollection) {
			$this->compositions = $compositions->toArray();
		} else {
			$this->compositions = $compositions;
		}

		return $this;
	}

	/**
	 * Starts the builder
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function build($options = array())
	{
		$this->setup();

		return 'yoyoyo';
	}

	/**
	 * Setups the builder by filling the arrays
	 * 
	 * @return TeamBuilder this
	 */
	private function setup()
	{
		$builds = array();
		foreach ($this->compositions as $composition) {
			foreach ($composition->getAllBuilds() as $build) {
				if(!in_array($build, $builds)) {
					$builds[] = $build;
				}
			}

			$this->teams[] = new Team($composition);
		}

		foreach ($builds as $build) {
			$max_spots    = $this->getMaxSpots($build);
			$min_spots    = $this->getMinSpots($build);
			$player_count = $this->getPlayerCount($build);
			$flag         = $this->getFlagForBuild($min_spots, $max_spots, $player_count);
			$value = array();
			$value['build']       = $build;
			$value['flag']        = $flag;
			$value['maxSpots']    = $max_spots;
			$value['minSpots']	  = $min_spots;
			$value['playerCount'] = $player_count;
			$this->builds[] = $value;
		}

		foreach ($this->players as $player) {
			if($this->isPlayerCritical($player)) {
				$this->critical_players[] = $player;
			}
		}

		$this->orderPlayers();
		$this->orderBuilds();

		return $this;
	}

	private function getFlagForBuild($minSpots, $maxSpots, $playerCount)
	{
		if ($playerCount < $maxSpots) {
			return TeamBuilder::FLAG_BUILD_IMPOSSIBLE;
		} else if ($maxSpots <= TeamBuilder::CEIL_CRITICAL_BUILD) {
			return TeamBuilder::FLAG_BUILD_CRITICAL;
		} else if ($maxSpots === $minSpots) {
			return TeamBuilder::FLAG_BUILD_STABLE;
		} else {
			return TeamBuilder::FLAG_BUILD_UNSTABLE;
		}
	}

	private function getPlayerCount(Build $build) 
	{
		$count = 0;
		foreach ($this->players as $player) {
			if($player->getPreferenceForBuild($build)->getLevel() >= 1) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Returns the maximum number of spots for a specific build
	 * 
	 * @param  Build  $build 
	 * 
	 * @return integer        
	 */
	private function getMaxSpots(Build $build)
	{
		return max(array_map(function($value) use($build) {
			return $value->getSpotsTotal($build);
		}, $this->teams));
	}

	/**
	 * Returns the minimum number of spots for a specific build
	 * 
	 * @param  Build  $build 
	 * 
	 * @return integer        
	 */
	private function getMinSpots(Build $build)
	{
		return min(array_map(function($value) use($build) {
			return $value->getSpotsTotal($build);
		}, $this->teams));
	}

	/**
	 * Returns whether a player is considered critical
	 * 
	 * @param  Player  $player 
	 * 
	 * @return boolean         
	 */
	private function isPlayerCritical(Player $player)
	{
		return $player
			->getNumberOfPlayableBuilds(TeamBuilder::CEIL_CRITICAL_PLAYER_LEVEL) 
			<= TeamBuilder::CEIL_CRITICAL_PLAYER_COUNT
		;
	}

	/**
	 * Returns whether a build has player assigned in all teams
	 * 
	 * @param  Build  $build 
	 * 
	 * @return boolean        
	 */
	private function buildDone(Build $build)
	{
		return 
			array_reduce(
				array_map(
					"getTeamSpotsLeft", 
					$this->teams
				),
				function ($result, $current) {
					return $result + $current;
				}
			) === 0
		;
	}

	/**
	 * Returns whether the player has a assignment in all teams
	 * 
	 * @param  Player $player 
	 * 
	 * @return boolean
	 */
	private function playerDone(Player $player)
	{
		foreach ($this->teams as $team) {
			if(!$team->isAssigned($player)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Orders the players depending on their number of playable builds
	 * 
	 * @return TeamBuilder this
	 */
	private function orderPlayers()
	{
		$min_pref = 1;
		
		usort(
			$this->players,
			function($a, $b) use($min_pref) {
				return $a->getNumberOfPlayableBuilds($min_pref) - $b->getNumberOfPlayableBuilds($min_pref);
			}
		);

		return $this;
	}

	/**
	 * Orders the builds depending on the difference between the max number of spot 
	 * and the count of players able to play this build
	 * 
	 * @return TeamBuilder this
	 */
	private function orderBuilds()
	{
		usort(
			$this->builds,
			function($a, $b) {
				$diff_a = $a['playerCount'] - $a['maxSpots'];
				$diff_b = $b['playerCount'] - $b['maxSpots'];
				return $diff_a - $diff_b;
			}
		);

		return $this;
	}

    /**
     * Gets the array of teams.
     *
     * @return array
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Gets the array of compositions.
     *
     * @return array
     */
    public function getCompositions()
    {
        return $this->compositions;
    }

    /**
     * Gets the array of players.
     *
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Gets the array of critical_players.
     *
     * @return array
     */
    public function getCriticalPlayers()
    {
        return $this->critical_players;
    }

    /**
     * Gets the array of builds with flag
     * 
     * @param  integer $flag
     *  
     * @return array       
     */
    public function getBuildsWithFlag($flag)
    {
        return array_map(
        	function($value) {
        		return $value['build'];
        	},
        	array_filter(
	        	$this->builds,
	        	function($value) use($flag) {
	        		return $value['flag'] === $flag;
	        	}
        	)
        );
    }

    /**
     * Gets the array of critical_builds.
     *
     * @return array
     */
    public function getCriticalBuilds()
    {
    	return $this->getBuildsWithFlag(TeamBuilder::FLAG_BUILD_CRITICAL);
    }

    /**
     * Gets the array of impossible_builds.
     *
     * @return array
     */
    public function getImpossibleBuilds()
    {
    	return $this->getBuildsWithFlag(TeamBuilder::FLAG_BUILD_IMPOSSIBLE);
    }

    /**
     * Gets the array of unstable_builds.
     *
     * @return array
     */
    public function getUnstableBuilds()
    {
    	return $this->getBuildsWithFlag(TeamBuilder::FLAG_BUILD_UNSTABLE);
    }

    /**
     * Gets the array of stable_builds.
     *
     * @return array
     */
    public function getStableBuilds()
    {
    	return $this->getBuildsWithFlag(TeamBuilder::FLAG_BUILD_STABLE);
    }

    /**
     * Gets the array of builds.
     *
     * @return array
     */
    public function getBuilds()
    {
        return array_map(
        	function($value) {
        		return $value['build'];
        	},
        	$this->builds
        );
    }

    /**
     * Gets the names of compositions.
     *
     * @return string
     */
    public function getCompositionsAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->compositions));
    }

    /**
     * Gets the names of players.
     *
     * @return string
     */
    public function getPlayersAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->players));
    }

    /**
     * Gets the names of critical_players.
     *
     * @return string
     */
    public function getCriticalPlayersAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->critical_players));
    }

    /**
     * Gets the names of critical_builds.
     *
     * @return string
     */
    public function getCriticalBuildsAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->getCriticalBuilds()));
    }

    /**
     * Gets the names of unstable_builds.
     *
     * @return string
     */
    public function getUnstableBuildsAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->getUnstableBuilds()));
    }

    /**
     * Gets the names of stable_builds.
     *
     * @return string
     */
    public function getStableBuildsAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->getStableBuilds()));
    }

    /**
     * Gets the names of builds.
     *
     * @return string
     */
    public function getBuildsAsString()
    {
        return join(', ', array_map(function($value) {return $value->getName();}, $this->getBuilds()));
    }
}

 ?>