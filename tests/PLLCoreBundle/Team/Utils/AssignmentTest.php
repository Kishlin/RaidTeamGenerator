<?php

namespace PLLCoreBundle\Tests\Team\Utils;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Team\Utils\Assignment;

class AssignmentTest extends TestCase
{
	public function mainProvider()
	{
		$player = new Player();
		$player->setName("Player");

		$assignment = new Assignment();

		return array(array($assignment, $player));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testAssignment($assignment, $player)
	{
		$this->assertFalse($assignment->isAssigned());
		$this->assertNull($assignment->getPlayer());

		$assignment->assign($player);

		$this->assertTrue($assignment->isAssigned());
		$this->assertEquals("Player", $assignment->getPlayer()->getName());

		$assignment->unassign();
		
		$this->assertFalse($assignment->isAssigned());
		$this->assertNull($assignment->getPlayer());
	}
}
