<?php

namespace PLLCoreBundle\Tests\Service;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Service\DefaultBuilds;
use PLL\CoreBundle\Factory\BuildFactory;

class DefaultBuildsTest extends TestCase
{
	public function mainProvider()
	{
		$service = new DefaultBuilds(new BuildFactory());
		$builds = $service->getDefaultBuilds();

		$expectedbuilds = array(
			0  => array("Power Berserker", "Berserker", "None"),
			1  => array("Condi Berserker", "Berserker", "Burning"),
			2  => array("Tempest Staff", "Tempest", "None"),
			3  => array("Tempest Fresh Air Staff", "Tempest", "None"),
			4  => array("Dragonhunter", "Dragonhunter", "None"),
			5  => array("Daredeveil", "Daredevil", "None"),
			6  => array("Chronomancer", "Chronomancer", "None"),
			7  => array("Condi Mesmer", "Mesmer", "Torment"),
			8  => array("Power Druid", "Druid", "None"),
			9 => array("Healing Druid", "Druid", "Regeneration"),
			10 => array("Condi Druid", "Druid", "Burning"),
			11 => array("Condi Ranger", "Ranger", "Burning"),
			12 => array("Condi Engineer", "Engineer", "Burning"),
			13 => array("Condi Reaper", "Reaper", "Bleeding"),
			14 => array("Herald", "Herald", "None"),
		);

		return array(array($builds, $expectedbuilds));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testBuilds($builds, $expectedbuilds)
	{
		$this->assertCount(15, $builds);

		for ($i=0; $i < 15; $i++) { 
			$this->assertEquals($builds[$i]->getName(), $expectedbuilds[$i][0]);
			$this->assertEquals($builds[$i]->getImg(), $expectedbuilds[$i][1]);
			$this->assertEquals($builds[$i]->getImgsub(), $expectedbuilds[$i][2]);
		}
	}
}

?>