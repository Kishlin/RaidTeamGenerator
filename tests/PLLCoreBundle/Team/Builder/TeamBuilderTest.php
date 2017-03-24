<?php

namespace PLLCoreBundle\Tests\Team\Builder;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\CompositionBuild;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Preference;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Build;

use PLL\CoreBundle\Team\Builder\TeamBuilder;

class TeamBuilderTest extends TestCase
{
	public function getMethod($name)
	{
		$class = new \ReflectionClass('PLL\CoreBundle\Team\Builder\TeamBuilder');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetFlagForBuild($builder, $compositions, $players, $builds)
	{
		$method = $this->getMethod('getFlagForBuild');
		$this->assertEquals(TeamBuilder::FLAG_BUILD_IMPOSSIBLE, $method->invokeArgs($builder, array(0, 1, 0)));
		$this->assertEquals(TeamBuilder::FLAG_BUILD_CRITICAL,   $method->invokeArgs($builder, array(0, 1, 1)));
		$this->assertEquals(TeamBuilder::FLAG_BUILD_STABLE,     $method->invokeArgs($builder, array(2, 2, 2)));
		$this->assertEquals(TeamBuilder::FLAG_BUILD_UNSTABLE,   $method->invokeArgs($builder, array(0, 2, 2)));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetPlayerCount($builder, $compositions, $players, $builds)
	{
		$method = $this->getMethod('getPlayerCount');
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['C0'])));
		$this->assertEquals(2, $method->invokeArgs($builder, array($builds['C1'])));
		$this->assertEquals(3, $method->invokeArgs($builder, array($builds['CS0'])));
		$this->assertEquals(2, $method->invokeArgs($builder, array($builds['S0'])));
		$this->assertEquals(6, $method->invokeArgs($builder, array($builds['S1'])));
		$this->assertEquals(4, $method->invokeArgs($builder, array($builds['U0'])));
		$this->assertEquals(8, $method->invokeArgs($builder, array($builds['U1'])));
		$this->assertEquals(0, $method->invokeArgs($builder, array($builds['I0'])));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetMaxSpots($builder, $compositions, $players, $builds)
	{
		$method = $this->getMethod('getMaxSpots');
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['C0'])));
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['C1'])));
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['CS0'])));
		$this->assertEquals(2, $method->invokeArgs($builder, array($builds['S0'])));
		$this->assertEquals(3, $method->invokeArgs($builder, array($builds['S1'])));
		$this->assertEquals(2, $method->invokeArgs($builder, array($builds['U0'])));
		$this->assertEquals(2, $method->invokeArgs($builder, array($builds['U1'])));
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['I0'])));
		$this->assertEquals(0, $method->invokeArgs($builder, array(new Build())));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetMinSpots($builder, $compositions, $players, $builds)
	{
		$method = $this->getMethod('getMinSpots');
		$this->assertEquals(0, $method->invokeArgs($builder, array($builds['C0'])));
		$this->assertEquals(0, $method->invokeArgs($builder, array($builds['C1'])));
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['CS0'])));
		$this->assertEquals(2, $method->invokeArgs($builder, array($builds['S0'])));
		$this->assertEquals(3, $method->invokeArgs($builder, array($builds['S1'])));
		$this->assertEquals(0, $method->invokeArgs($builder, array($builds['U0'])));
		$this->assertEquals(0, $method->invokeArgs($builder, array($builds['U1'])));
		$this->assertEquals(1, $method->invokeArgs($builder, array($builds['I0'])));
		$this->assertEquals(0, $method->invokeArgs($builder, array(new Build())));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testIsPlayerCritical($builder, $compositions, $players, $builds)
	{
		$method = $this->getMethod('isPlayerCritical');
		$this->assertTrue($method->invokeArgs($builder, array($players['PC0'])));
		$this->assertTrue($method->invokeArgs($builder, array($players['PC1'])));
		$this->assertFalse($method->invokeArgs($builder, array($players['P0'])));
		$this->assertFalse($method->invokeArgs($builder, array($players['P1'])));
		$this->assertFalse($method->invokeArgs($builder, array($players['P2'])));
		$this->assertFalse($method->invokeArgs($builder, array($players['P3'])));
		$this->assertFalse($method->invokeArgs($builder, array($players['P4'])));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGlobal($builder, $compositions, $players, $builds)
	{
		$this->assertRegExp("/^PC0,\s(PC\d,\s){2}(P\d,\s){6}P\d$/", $builder->getPlayersAsString());
		$this->assertRegExp("/^(PC\d,\s){2}PC\d$/", $builder->getCriticalPlayersAsString());
		$this->assertEquals("C0, C1, CS0", $builder->getCriticalBuildsAsString());
		$this->assertEquals("S0, S1", $builder->getStableBuildsAsString());
		$this->assertEquals("U0, U1", $builder->getUnstableBuildsAsString());
	}

	public function mainProvider()
	{
		$b_critical_0 	   = new Build();
		$b_critical_1 	   = new Build();
		$b_critical_stable = new Build();
		$b_stable_0   	   = new Build();
		$b_stable_1   	   = new Build();
		$b_unstable_0 	   = new Build();
		$b_unstable_1      = new Build();
		$b_impossible      = new Build();
		$b_critical_0     ->setName("C0");
		$b_critical_1     ->setName("C1");
		$b_critical_stable->setName("CS0");
		$b_stable_0  	  ->setName("S0");
		$b_stable_1  	  ->setName("S1");
		$b_unstable_0	  ->setName("U0");
		$b_unstable_1	  ->setName("U1");
		$b_impossible     ->setName("I0");
		$b_critical_0     = $this->setBuildId($b_critical_0, 0);
		$b_critical_1     = $this->setBuildId($b_critical_1, 1);
		$b_critical_stable= $this->setBuildId($b_critical_stable, 2);
		$b_stable_0  	  = $this->setBuildId($b_stable_0, 3);
		$b_stable_1  	  = $this->setBuildId($b_stable_1, 4);
		$b_unstable_0	  = $this->setBuildId($b_unstable_0, 5);
		$b_unstable_1	  = $this->setBuildId($b_unstable_1, 6);
		$b_impossible     = $this->setBuildId($b_impossible, 7);

		$p_critical_0 = new Player();
		$p_critical_0->setName("PC0");
		$p_critical_1 = new Player();
		$p_critical_1->setName("PC1");
		$p_critical_2 = new Player();
		$p_critical_2->setName("PC2");
		$p0 = new Player();
		$p0->setName("P0");
		$p1 = new Player();
		$p1->setName("P1");
		$p2 = new Player();
		$p2->setName("P2");
		$p3 = new Player();
		$p3->setName("P3");
		$p4 = new Player();
		$p4->setName("P4");
		$p5 = new Player();
		$p5->setName("P5");
		$p6 = new Player();
		$p6->setName("P6");

		$composition_0 = new Composition();
		$cb = new CompositionBuild();
		$cb->setBuild($b_critical_0);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_critical_stable);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_unstable_0);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_unstable_0);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_impossible);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_0);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_0);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_1);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_1);
		$composition_0->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_1);
		$composition_0->addCompositionbuild($cb);

		$composition_1 = new Composition();
		$cb = new CompositionBuild();
		$cb->setBuild($b_critical_1);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_critical_stable);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_unstable_1);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_unstable_1);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_impossible);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_0);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_0);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_1);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_1);
		$composition_1->addCompositionbuild($cb);
		$cb = new CompositionBuild();
		$cb->setBuild($b_stable_1);
		$composition_1->addCompositionbuild($cb);

		$compositions = array($composition_0, $composition_1);
		$players 	  = array(
			'PC0' => $p_critical_0, 
			'PC1' => $p_critical_1, 
			'PC2' => $p_critical_2, 
			'P0'  => $p0, 
			'P1'  => $p1, 
			'P2'  => $p2, 
			'P3'  => $p3, 
			'P4'  => $p4,
			'P5'  => $p5,
			'P6'  => $p6,
		);
		$builds 	  = array(
			'C0'  => $b_critical_0,
			'C1'  => $b_critical_1,
			'CS0' => $b_critical_stable,
			'S0'  => $b_stable_0,
			'S1'  => $b_stable_1,
			'U0'  => $b_unstable_0,
			'U1'  => $b_unstable_1,
			'I0'  => $b_impossible,
		);

		foreach ($builds as $build) {
			foreach ($players as $player) {
				$p = new Preference();
				$p->setBuild($build);
				$p->setPlayer($player);
				$p->setLevel(0);
			}
		}

		$p0->setPreferenceForBuild($b_critical_0, 8);
		$p1->setPreferenceForBuild($b_critical_1, 8);
		$p2->setPreferenceForBuild($b_critical_1, 8);
		$p3->setPreferenceForBuild($b_critical_stable, 8);
		$p4->setPreferenceForBuild($b_critical_stable, 8);
		$p5->setPreferenceForBuild($b_critical_stable, 8);
		$p6->setPreferenceForBuild($b_stable_0, 8);
		$p0->setPreferenceForBuild($b_stable_0, 8);
		$p1->setPreferenceForBuild($b_stable_1, 8);
		$p2->setPreferenceForBuild($b_stable_1, 8);
		$p3->setPreferenceForBuild($b_stable_1, 8);
		$p4->setPreferenceForBuild($b_unstable_0, 8);
		$p5->setPreferenceForBuild($b_unstable_0, 8);
		$p6->setPreferenceForBuild($b_unstable_0, 8);
		$p0->setPreferenceForBuild($b_unstable_1, 8);
		$p1->setPreferenceForBuild($b_unstable_1, 8);
		$p2->setPreferenceForBuild($b_unstable_1, 8);
		$p3->setPreferenceForBuild($b_unstable_1, 8);
		$p4->setPreferenceForBuild($b_unstable_1, 8);
		$p5->setPreferenceForBuild($b_unstable_1, 8);
		$p6->setPreferenceForBuild($b_unstable_1, 8);

		$p_critical_0->setPreferenceForBuild($b_stable_1, 8);
		$p_critical_1->setPreferenceForBuild($b_stable_1, 8);
		$p_critical_1->setPreferenceForBuild($b_unstable_1, 8);
		$p_critical_2->setPreferenceForBuild($b_stable_1, 8);
		$p_critical_2->setPreferenceForBuild($b_unstable_0, 8);

		$builder = new TeamBuilder();
		$builder
			->setPlayers($players)
			->setCompositions($compositions)
			->build()
		;

		return array(array($builder, $compositions, $players, $builds));
	}

	public function setBuildId($build, $id)
	{
		$reflector = new \ReflectionProperty('PLL\CoreBundle\Entity\Build', 'id');
		$reflector->setAccessible(true);
		$reflector->setValue($build, $id);
		return $build;
	}
}
