<?php

namespace PLLCoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Build;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Preference;

class PlayerTest extends TestCase
{
	public function setBuildId($build, $id)
	{
		$reflector = new \ReflectionProperty('Build', 'id');
		$reflector->setAccessible(true);
		$reflector->setValue($build, $id);
		return $build;
	}

	public function playerProvider()
	{
		$b = new Build();
		$b->setName("B0");
		$b = $this->setBuildId($b, 0);
		$b1 = new Build();
		$b1->setName("B1");
		$b1 = $this->setBuildId($b1, 1);
		$b2 = new Build();
		$b2 = $this->setBuildId($b2, 2);
		$b2->setName("B2");

		$player = new Player();

		$p = new Preference();
		$p->setPlayer($player);
		$p->setBuild($b);
		$p->setLevel(10);

		$p1 = new Preference();
		$p1->setPlayer($player);
		$p1->setBuild($b1);
		$p1->setLevel(5);

		return array(array($player, $b, $b1, $b2));
	}

	/**
	 * @dataProvider playerProvider
	 */
	public function testGetPreferenceForBuild($player, $assigned_build_10, $assigned_build_5, $unassigned_build)
	{
		$this->assertNull($player->getPreferenceForBuild($unassigned_build));
		$this->assertEquals(10, $player->getPreferenceForBuild($assigned_build_10)->getLevel());
		$this->assertEquals(5,  $player->getPreferenceForBuild($assigned_build_5) ->getLevel());
	}

	/**
	 * @dataProvider playerProvider
	 */
	public function testSetPreferenceForBuild($player, $assigned_build_10, $assigned_build_5, $unassigned_build)
	{
		$player->setPreferenceForBuild($assigned_build_10, 5);
		$this->assertEquals(5, $player->getPreferenceForBuild($assigned_build_10)->getLevel());
		$this->assertCount(2, $player->getPreferences());
		$player->setPreferenceForBuild($unassigned_build, 10);
		$this->assertNull($player->getPreferenceForBuild($unassigned_build));
		$this->assertCount(2, $player->getPreferences());
	}

	/**
	 * @dataProvider playerProvider
	 */
    public function testGetNumberOfPlayableBuilds($player, $assigned_build_10, $assigned_build_5, $unassigned_build)
    {
		$this->assertEquals(2, $player->getNumberOfPlayableBuilds(0));
		$this->assertEquals(2, $player->getNumberOfPlayableBuilds(5));
		$this->assertEquals(1, $player->getNumberOfPlayableBuilds(7));
		$this->assertEquals(1, $player->getNumberOfPlayableBuilds(10));
		$this->assertEquals(0, $player->getNumberOfPlayableBuilds(11));
    }

	/**
	 * @dataProvider playerProvider
	 */
    public function testGetPlayable($player, $assigned_build_10, $assigned_build_5, $unassigned_build)
    {
    	foreach(array(0, 3, 5) as $pref) {
	    	$builds = $player->getPlayable($pref);
	    	$this->assertContains($assigned_build_10, $builds);
	    	$this->assertContains($assigned_build_5,  $builds);
	    	$this->assertCount(2, $builds);
    	}

    	foreach(array(7, 10) as $pref) {
	    	$builds = $player->getPlayable($pref);
	    	$this->assertCount(1, $builds);
	    	$this->assertContains($assigned_build_10, $builds);
    	}

    	$builds = $player->getPlayable(11);
    	$this->assertCount(0, $builds);
    }
}
