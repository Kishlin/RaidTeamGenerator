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
	 * @var logger
	 */
	private $logger = null;

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
	 * Sets the logger
	 * 
	 * @param logger $logger 
	 */
	public function setLogger($logger)
	{
		$this->logger = $logger;

		return $this;
	}

	/**
	 * Sets the players
	 * 
	 * @param array $players
	 */
	public function setPlayers($players)
	{
		if(is_array($players)) {
			$this->players = $players;
		} else {
			$this->players = $players->toArray();
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
		if(is_array($compositions)) {
			$this->compositions = $compositions;
		} else {
			$this->compositions = $compositions->toArray();
		}

		return $this;
	}

	/**
	 * Starts the builder
	 * 
	 * @param  array  $options 
	 * 
	 * @return string          message
	 */
	public function build($options = array())
	{
		$this->setup();

		// $this->logger->debug("TeamBuilder setup complete.");

		//foreach ($this->teams as $team) {
			// $this->logger->debug($team->toString());
		//}

		// $this->logger->debug("Players => " . $this->getPlayersAsString());
		// $this->logger->debug("Critical Players => " . $this->getCriticalPlayersAsString());
		// $this->logger->debug("Builds => " . $this->getBuildsAsString());
		// $this->logger->debug("Impossible Builds => " . $this->getImpossibleBuildsAsString());
		// $this->logger->debug("Critical Builds => " . $this->getCriticalBuildsAsString());
		// $this->logger->debug("Stable Builds => " . $this->getStableBuildsAsString());
		// $this->logger->debug("Unstable Builds => " . $this->getUnstableBuildsAsString());

		foreach ($this->getCriticalPlayers() as $player) {
			$this->process_player($player);
		}

		// $this->logger->debug("Done with Critical Players !");
		//foreach ($this->teams as $team) {
			// $this->logger->debug($team->toString());
		//}

		foreach ($this->getCriticalBuilds() as $build) {
			$this->process_build($build);
		}

		// $this->logger->debug("Done with Critical Builds !");
		//foreach ($this->teams as $team) {
			// $this->logger->debug($team->toString());
		//}

		foreach ($this->getStableBuilds() as $build) {
			$this->process_build($build);
		}

		// $this->logger->debug("Done with Stable Builds !");
		//foreach ($this->teams as $team) {
			// $this->logger->debug($team->toString());
		//}

		foreach ($this->getUnstableBuilds() as $build) {
			$this->process_build($build);
		}

		// $this->logger->debug("Done with Unstable Builds !");
		//foreach ($this->teams as $team) {
			// $this->logger->debug($team->toString());
		//}

		foreach ($this->teams as $team) {
			if(!$team->isComplete()) {
				$team->attemptFilling($this->players, $this->logger);
			}
		}

		$messages = array();

		foreach ($this->teams as $team) {
			if(!$team->isComplete()) {
				$messages['error'] = 'team.error.incomplete';
				break;
			}
		}

		if($this->getImpossibleBuildsAsString() !== "") {
			$messages['impossible'] = $this->getImpossibleBuildsAsString();
		}

		return $messages;
	}

	private function tryAssigning(Player $player, $builds)
	{
		$assignments_made = false;

		foreach ($builds as $build) {
			foreach($this->teams as $team) {
				if(!$team->isAssigned($player) && $team->getSpotsLeft($build) > 0) {
					$team->assign($player, $build);
					$assignments_made = true;
				}
			}
		}
		
		return $assignments_made;
	}

	private function process_player(Player $player)
	{
		$min_pref = 8;

		do {
			$builds = $player->getPlayable($min_pref);

			$builds_playable_stable   = array_map('unserialize', 
				array_intersect(array_map('serialize', $builds), array_map('serialize', $this->getStableBuilds())));
			$builds_playable_critical = array_map('unserialize', 
				array_intersect(array_map('serialize', $builds), array_map('serialize', $this->getCriticalBuilds())));
			$builds_playable_unstable = array_map('unserialize', 
				array_intersect(array_map('serialize', $builds), array_map('serialize', $this->getUnstableBuilds())));

			if(!$this->tryAssigning($player, $builds_playable_critical)) {
				$this->tryAssigning($player, $builds_playable_stable);
				if(!$this->playerDone($player)) {
					$this->tryAssigning($player, $builds_playable_unstable);
				}
			} else {
				$this->tryAssigning($player, $builds_playable_unstable);
				if(!$this->playerDone($player)) {
					$this->tryAssigning($player, $builds_playable_stable);
				}
			}

			if($min_pref > 2) {
				$min_pref -= 2;
			} else {
				return false;
			}
		} while(!$this->playerDone($player));

		return true;
	}

	private function getRandomPlayer(Build $build, $min_pref)
	{
		$players = array();
		foreach ($this->players as $player) {
			if(!$this->playerDone($player) && $player->getPreferenceForBuild($build)->getLevel() >= $min_pref) {
				$players[] = $player;
			}
		}

		// $this->logger->debug("Possible players : " . join(', ', array_map(function($value) {return $value->getName();}, $players)));

		if(count($players) > 0) {
			$player = $players[mt_rand(0, count($players) - 1)];
			// $this->logger->debug("Returning player : " . $player->getName());
			return $player;
		} else {
			// $this->logger->debug("Returning null.");
			return null;
		}
	}

	private function process_build(Build $build)
	{
		$min_pref = 8;
		$loops = 0;

		// $this->logger->debug("Now processing build : " . $build->getName());

		do {
			$player = $this->getRandomPlayer($build, $min_pref);
			if($player !== null) {
				foreach ($this->teams as $team) {
					// $this->logger->debug("Now looking at team : " . $team->toString());
					// $this->logger->debug("Spots left : " . $team->getSpotsLeft($build));
					// $this->logger->debug(($team->isAssigned($player)) ? "isAssigned returned true" : "isAssigned returned false");
					if(!$team->isAssigned($player) && $team->getSpotsLeft($build) > 0) {
						// $this->logger->debug("Assigning player " . $player->getName() . " to build " . $build->getName());
						$team->assign($player, $build);
						// $this->logger->debug("Spots left : " . $team->getSpotsLeft($build));
					}
				}
			}

			if($loops < 3) {
				$loops++;
			} else if($min_pref > 2) {
				$min_pref -= 2;
				$loops = 0;
			} else {
				// $this->logger->debug("Could not fully assign : " . $build->getName());
				return false;
			}
		} while(!$this->buildDone($build));

		// $this->logger->debug("Build done : " . $build->getName());

		return true;
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
		foreach ($this->teams as $team) {
			// $this->logger->debug("Spots left In Build Done : " . $team->getSpotsLeft($build));
			if($team->getSpotsLeft($build) > 0) {
				return false;
			}
		}

		return true;
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
     * Gets the names of impossible_builds.
     * 
     * @return string 
     */
    public function getImpossibleBuildsAsString()
    {
    	return join(', ', array_map(function($value) {return $value->getName();}, $this->getImpossibleBuilds()));
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