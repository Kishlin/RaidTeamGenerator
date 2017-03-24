<?php

namespace PLLCoreBundle\Tests\Team\Validator;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Event;
use PLL\UserBundle\Entity\Guild;

use PLL\CoreBundle\Team\Validator\TeamValidator;

class TeamValidatorTest extends TestCase
{
	public function getTwoPlayers(Guild $guild)
	{
		$players = array(new Player(), new Player());

		array_walk(
			$players,
			function($e) use($guild) {
				$guild->addPlayer($e);
			}
		);

		return $players;
	}

	public function getTwoCompositions(Guild $guild)
	{
		$compositions = array(new Composition(), new Composition());

		array_walk(
			$compositions,
			function($e) use($guild) {
				$e->setSize(2);
				$guild->addComposition($e);
			}
		);

		return $compositions;
	}

	public function playerCompProvider()
	{
		$guild = new Guild();

		$players      = $this->getTwoPlayers($guild);
		$compositions = $this->getTwoCompositions($guild);

		$validator = new TeamValidator();

		return array(array($validator, $guild, $players, $compositions));
	}

	public function eventProvider()
	{
		$guild = new Guild();

		$players      = $this->getTwoPlayers($guild);
		$compositions = $this->getTwoCompositions($guild);
	
		$event = new Event();
		$guild->addEvent($event);

		foreach ($players as $player) {
			$event->addPlayer($player);
		}

		foreach ($compositions as $composition) {
			$event->addComposition($composition);
		}

		$validator = new TeamValidator();

		return array(array($validator, $guild, $event));
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testValidPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$validator->setupWithPlayersAndCompositions($players, $compositions);
		$this->assertNull($validator->validate($guild));
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testValidEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$validator->setupWithEvent($event);
		$this->assertNull($validator->validate($guild));
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testWrongGuildEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$validator->setupWithEvent($event);
		$guild->removeEvent($event);
		$event->setGuild(new Guild());
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_GUILD_EVENT);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testNoPlayersPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$validator->setupWithPlayersAndCompositions(array(), $compositions);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_NO_PLAYERS);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testNoPlayersEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		foreach ($event->getPlayers() as $player) {
			$event->removePlayer($player);
		}

		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_NO_PLAYERS);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testNoCompositionsPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$validator->setupWithPlayersAndCompositions($players, array());
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_NO_COMPOSITIONS);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testNoCompositionsEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		foreach ($event->getCompositions() as $composition) {
			$event->removeComposition($composition);
		}

		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_NO_COMPOSITIONS);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testWrongPlayerGuildPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$guild->removePlayer($players[0]);
		$players[0]->setGuild(new Guild());
		$validator->setupWithPlayersAndCompositions($players, $compositions);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_GUILD_PLAYER);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testWrongPlayerGuildEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$guild->removePlayer($event->getPlayers()->first());
		$event->getPlayers()->first()->setGuild(new Guild());
		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_GUILD_PLAYER);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testWrongCompositionGuildPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$guild->removeComposition($compositions[0]);
		$compositions[0]->setGuild(new Guild());
		$validator->setupWithPlayersAndCompositions($players, $compositions);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_GUILD_COMPOSITION);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testWrongCompositionGuildEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$guild->removeComposition($event->getCompositions()->first());
		$event->getCompositions()->first()->setGuild(new Guild());
		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_GUILD_COMPOSITION);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testCompositionSizePlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$compositions[0]->setSize(1);
		$validator->setupWithPlayersAndCompositions($players, $compositions);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_COMPOSITIONS_SIZE);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testCompositionSizeGuildEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$event->getCompositions()->first()->setSize(1);
		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_COMPOSITIONS_SIZE);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testTooManyPlayersPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$p = new Player();
		$guild->addPlayer($p);
		$players[] = $p;
		$validator->setupWithPlayersAndCompositions($players, $compositions);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_PLAYERS_COUNT);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testTooManyPlayersEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$p = new Player();
		$guild->addPlayer($p);
		$event->addPlayer($p);
		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_PLAYERS_COUNT);
	}

	/**
	 * @dataProvider playerCompProvider
	 */
	public function testTooFewPlayersPlayerComp(TeamValidator $validator, Guild $guild, array $players, array $compositions)
	{
		$players = array_slice($players, 1);
		$validator->setupWithPlayersAndCompositions($players, $compositions);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_PLAYERS_COUNT);
	}

	/**
	 * @dataProvider eventProvider
	 */
	public function testTooFewPlayersEvent(TeamValidator $validator, Guild $guild, Event $event)
	{
		$event->removePlayer($event->getPlayers()->first());
		$validator->setupWithEvent($event);
		$this->assertEquals($validator->validate($guild), TeamValidator::ERROR_PLAYERS_COUNT);
	}
}

?>