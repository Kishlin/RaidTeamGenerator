<?php 

namespace PLL\CoreBundle\Team\Validator;

use PLL\CoreBundle\Entity\CompositionGroupBuild;
use PLL\CoreBundle\Entity\CompositionGroup;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Event;
use PLL\CoreBundle\Entity\Build;
use PLL\UserBundle\Entity\Guild;

class TeamValidator
{
	CONST ERROR_GUILD_COMPOSITION    = 'event.error.guild.composition';
	CONST ERROR_COMPOSITIONS_SIZE    = 'event.error.composition.size';
	CONST ERROR_NO_COMPOSITIONS      = 'event.error.composition.none';
	CONST ERROR_PLAYERS_COUNT        = 'event.error.player.count';
	CONST ERROR_GUILD_PLAYER 	     = 'event.error.guild.player';
	CONST ERROR_GUILD_EVENT     	 = 'event.error.guild.event';
	CONST ERROR_NO_PLAYERS 	    	 = 'event.error.player.none';

	private $event 		  = null;
	private $players 	  = array();
	private $compositions = array();

	public function getPlayers()
	{
		return $this->players;
	}

	public function getCompositions()
	{
		return $this->compositions;
	}

	public function setupWithEvent(Event $event)
	{
		$this->event = $event;
		$this->players = $event->getPlayers();
		$this->compositions = $event->getCompositions();

		return $this;
	}

	public function setupWithPlayersAndCompositions($players, $compositions) 
	{
		$this->players = $players;
		$this->compositions = $compositions;

		return $this;
	}

	public function validate(Guild $guild)
	{
		if ($this->event !== null && $this->event->getGuild() !== $guild) {
			return TeamValidator::ERROR_GUILD_EVENT;
		} else if (count($this->players) === 0) {
			return TeamValidator::ERROR_NO_PLAYERS;
		} else if (count($this->compositions) === 0) {
			return TeamValidator::ERROR_NO_COMPOSITIONS;
		}

		foreach ($this->players as $player) {
			if($player->getGuild() !== $guild) {
				return TeamValidator::ERROR_GUILD_PLAYER;
			}
		}

		foreach ($this->compositions as $composition) {
			if($composition->getGuild() !== $guild) {
				return TeamValidator::ERROR_GUILD_COMPOSITION;
			}
		}

		$size = $this->compositions[0]->getSize();
		for ($i=1, $max = count($this->compositions); $i < $max; $i++) { 
			if($size !== $this->compositions[$i]->getSize()) {
				return TeamValidator::ERROR_COMPOSITIONS_SIZE;
			}
		}

		if(count($this->players) !== $size) {
			return TeamValidator::ERROR_PLAYERS_COUNT;
		}
	}
}

 ?>