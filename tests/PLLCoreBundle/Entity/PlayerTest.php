<?php

namespace PLLCoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Build;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Preference;

class PlayerTest extends TestCase
{
	public function playerProvider()
	{
		$b = new Build();
		$b->setName("B0");
		$b2 = new Build();
		$b2->setName("B1");

		$player = new Player();

		$p = new Preference();
		$p->setPlayer($player);
		$p->setBuild($b);
		$p->setLevel(10);

		return array(array($player, $b, $b2));
	}

	/**
	 * @dataProvider playerProvider
	 */
	public function testGetPreferenceForBuild($player, $assigned_build, $unassigned_build)
	{
		$this->assertNull($player->getPreferenceForBuild($unassigned_build));
		$this->assertEquals(10, $player->getPreferenceForBuild($assigned_build)->getLevel());
	}

	/**
	 * @dataProvider playerProvider
	 */
	public function testSetPreferenceForBuild($player, $assigned_build, $unassigned_build)
	{
		$player->setPreferenceForBuild($assigned_build, 5);
		$this->assertEquals(5, $player->getPreferenceForBuild($assigned_build)->getLevel());
		$player->setPreferenceForBuild($unassigned_build, 10);
		$this->assertNull($player->getPreferenceForBuild($unassigned_build));
		$this->assertCount(1, $player->getPreferences());
	}
}
