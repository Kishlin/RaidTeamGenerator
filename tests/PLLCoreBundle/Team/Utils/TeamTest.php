<?php

namespace PLLCoreBundle\Tests\Team\Utils;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\CompositionBuild;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Build;

use PLL\CoreBundle\Team\Exception\AlreadyAssignedException;
use PLL\CoreBundle\Team\Exception\CouldNotAssignException;
use PLL\CoreBundle\Team\Exception\NoFreeSpotException;

use PLL\CoreBundle\Team\Utils\Assignment;
use PLL\CoreBundle\Team\Utils\Team;

class TeamTest extends TestCase
{
	public function mainProvider()
	{
		$composition = new Composition();
		$composition->setSize(3);

		$p1 = new Player();
		$p1->setName("P0");
		$p2 = new Player();
		$p2->setName("P1");
		$p3 = new Player();
		$p3->setName("P2");
		$p4 = new Player();
		$p4->setName("P0");

		$players = array($p1, $p2, $p3, $p4);

		$b_double = new Build();
		$b_double->setName("BDouble");
		$b_single = new Build();
		$b_single->setName("BSingle");
		$b_none = new Build();
		$b_none->setName("BNone");

		$cb_1 = new CompositionBuild();
		$cb_1->setBuild($b_double);
		$composition->addCompositionbuild($cb_1);

		$cb_2 = new CompositionBuild();
		$cb_2->setBuild($b_double);
		$composition->addCompositionbuild($cb_2);

		$cb_3 = new CompositionBuild();
		$cb_3->setBuild($b_single);
		$composition->addCompositionbuild($cb_3);

		$team = new Team($composition);

		return array(array($team, $b_double, $b_single, $b_none, $players));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testAlReadyAssignedException($team, $b_double, $b_single, $b_none, $players)
	{
		$team->assign($players[0], $b_double);
        $this->expectException(AlreadyAssignedException::class);
		$team->assign($players[0], $b_double);
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testNoFreeSpotExceptionOnBuildNone($team, $b_double, $b_single, $b_none, $players)
	{
		$this->expectException(NoFreeSpotException::class);
		$team->assign($players[0], $b_none);
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testNoFreeSpotExceptionOnBuildSingle($team, $b_double, $b_single, $b_none, $players)
	{
		$team->assign($players[0], $b_single);
		$this->expectException(NoFreeSpotException::class);
		$team->assign($players[1], $b_single);
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testNoFreeSpotExceptionOnBuildDouble($team, $b_double, $b_single, $b_none, $players)
	{
		$team->assign($players[0], $b_double);
		$team->assign($players[1], $b_double);
		$this->expectException(NoFreeSpotException::class);
		$team->assign($players[2], $b_double);
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetSize($team, $b_double, $b_single, $b_none, $players)
	{
		$this->assertEquals(3, $team->getSize());
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testIsComplete($team, $b_double, $b_single, $b_none, $players)
	{
		$this->assertFalse($team->isComplete());
		$team->assign($players[0], $b_single);
		$this->assertFalse($team->isComplete());
		$team->assign($players[1], $b_double);
		$this->assertFalse($team->isComplete());
		$team->assign($players[2], $b_double);
		$this->assertTrue($team->isComplete());
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testIsAssigned($team, $b_double, $b_single, $b_none, $players)
	{
		$this->assertFalse($team->isAssigned($players[0]));
		$team->assign($players[0], $b_single);
		$this->assertTrue($team->isAssigned($players[0]));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetSpotsTotal($team, $b_double, $b_single, $b_none, $players)
	{
		$this->assertEquals(0, $team->getSpotsTotal($b_none));
		$this->assertEquals(1, $team->getSpotsTotal($b_single));
		$this->assertEquals(2, $team->getSpotsTotal($b_double));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetSpotsLeft($team, $b_double, $b_single, $b_none, $players)
	{
		$this->assertEquals(2, $team->getSpotsLeft($b_double));
		$team->assign($players[0], $b_double);
		$this->assertEquals(1, $team->getSpotsLeft($b_double));
		$team->assign($players[1], $b_double);
		$this->assertEquals(0, $team->getSpotsLeft($b_double));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testGetPlayerAssigned($team, $b_double, $b_single, $b_none, $players)
	{
		$this->assertNull($team->getPlayerAssigned(0));
		$this->assertNull($team->getPlayerAssigned(1));
		$this->assertNull($team->getPlayerAssigned(2));
		$team->assign($players[0], $b_double);
		$this->assertEquals($players[0], $team->getPlayerAssigned(0));
		$team->assign($players[1], $b_double);
		$this->assertEquals($players[1], $team->getPlayerAssigned(1));
		$team->assign($players[2], $b_single);
		$this->assertEquals($players[2], $team->getPlayerAssigned(2));

		$this->expectException(\OutOfBoundsException::class);
		$team->getPlayerAssigned(3);
	}
}
