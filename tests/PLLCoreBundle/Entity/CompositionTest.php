<?php

namespace PLLCoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Build;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\CompositionBuild;

class CompositionTest extends TestCase
{
	public function getEmptyComposition()
	{
		$composition = new Composition();
		$composition->setSize(0)->setGroupscount(0);

		return $composition;
	}

	public function getSingleBuildComposition()
	{
		$build = new Build();
		$build->setName("G0B0");

		$cbuild = new CompositionBuild();
		$cbuild->setBuild($build)->setGroupindex(0);

		$composition = new Composition();
		$composition->addCompositionbuild($cbuild)->setSize(1)->setGroupscount(1);

		return $composition;
	}

	public function getSingleGroupComposition()
	{
		$composition = new Composition();

		for ($j=0; $j < 4; $j++) { 
			$build = new Build();
			$build->setName("G0B".$j);
			$cbuild = new CompositionBuild();
			$cbuild->setBuild($build)->setGroupindex(0);
			$composition->addCompositionbuild($cbuild);
		}

		$composition->setSize(4)->setGroupscount(1);

		return $composition;
	}

	public function getTwoGroupsComposition()
	{
		$composition = new Composition();

		for ($i=0; $i < 2; $i++) { 
			for ($j=0; $j < 4; $j++) { 
				$build = new Build();
				$build->setName("G".$i."B".$j);
				$cbuild = new CompositionBuild();
				$cbuild->setBuild($build)->setGroupindex($i);
				$composition->addCompositionbuild($cbuild);
			}
		}

		$composition->setSize(8)->setGroupscount(2);

		return $composition;
	}

	public function emptyCompositionProvider()
	{
		return array(array(
			$this->getEmptyComposition(),
		)); 
	}

	public function singleBuildCompositionProvider()
	{
		return array(array(
			$this->getSingleBuildComposition(),
		)); 
	}

	public function singleGroupCompositionProvider()
	{
		return array(array(
			$this->getSingleGroupComposition(),
		)); 
	}

	public function twoGroupsCompositionProvider()
	{
		return array(array(
			$this->getTwoGroupsComposition(),
		)); 
	}

	public function allCompositionsProvider()
	{
		return array(array(
			$this->getEmptyComposition(),
			$this->getSingleBuildComposition(),
			$this->getSingleGroupComposition(),
			$this->getTwoGroupsComposition(),
		)); 
	}

	/**
	 * @dataProvider allCompositionsProvider
	 */
	public function testGetGroup($c_empty, $c_unique, $c_single, $c_full)
	{
		$this->assertCount(0, $c_empty->getGroup(0));
		$this->assertCount(0, $c_empty->getGroup(1));

		$this->assertCount(1, 		$c_unique->getGroup(0));
		$this->assertCount(0, 		$c_unique->getGroup(1));
		$this->assertEquals("G0B0", $c_unique->getGroup(0)->get(0)->getBuild()->getName());

		$this->assertCount(4, 		$c_single->getGroup(0));
		$this->assertCount(0, 		$c_single->getGroup(1));
		$this->assertEquals("G0B0", $c_single->getGroup(0)->get(0)->getBuild()->getName());
		$this->assertEquals("G0B1", $c_single->getGroup(0)->get(1)->getBuild()->getName());
		$this->assertEquals("G0B2", $c_single->getGroup(0)->get(2)->getBuild()->getName());
		$this->assertEquals("G0B3", $c_single->getGroup(0)->get(3)->getBuild()->getName());

		$this->assertCount(4, 		$c_full->getGroup(0));
		$this->assertCount(4, 		$c_full->getGroup(1));
		$this->assertEquals("G0B0", $c_full->getGroup(0)->get(0)->getBuild()->getName());
		$this->assertEquals("G0B1", $c_full->getGroup(0)->get(1)->getBuild()->getName());
		$this->assertEquals("G0B2", $c_full->getGroup(0)->get(2)->getBuild()->getName());
		$this->assertEquals("G0B3", $c_full->getGroup(0)->get(3)->getBuild()->getName());
		$this->assertEquals("G1B0", $c_full->getGroup(1)->get(4)->getBuild()->getName());
		$this->assertEquals("G1B1", $c_full->getGroup(1)->get(5)->getBuild()->getName());
		$this->assertEquals("G1B2", $c_full->getGroup(1)->get(6)->getBuild()->getName());
		$this->assertEquals("G1B3", $c_full->getGroup(1)->get(7)->getBuild()->getName());
		$this->assertNull($c_full->getGroup(1)->get(0));
		$this->assertNull($c_full->getGroup(1)->get(1));
		$this->assertNull($c_full->getGroup(1)->get(2));
		$this->assertNull($c_full->getGroup(1)->get(3));
		$this->assertNull($c_full->getGroup(0)->get(4));
		$this->assertNull($c_full->getGroup(0)->get(5));
		$this->assertNull($c_full->getGroup(0)->get(6));
		$this->assertNull($c_full->getGroup(0)->get(7));
	}

	/**
	 * @dataProvider allCompositionsProvider
	 */
	public function testGetGroupSize($c_empty, $c_unique, $c_single, $c_full)
	{
		$this->assertEquals(0, $c_empty->getGroupSize(0));
		$this->assertEquals(0, $c_empty->getGroupSize(1));

		$this->assertEquals(1, $c_unique->getGroupSize(0));
		$this->assertEquals(0, $c_unique->getGroupSize(1));

		$this->assertEquals(4, $c_single->getGroupSize(0));
		$this->assertEquals(0, $c_single->getGroupSize(1));

		$this->assertEquals(4, $c_full->getGroupSize(0));
		$this->assertEquals(4, $c_full->getGroupSize(1));
		$this->assertEquals(0, $c_full->getGroupSize(2));
	}

	/**
	 * @dataProvider allCompositionsProvider
	 */
	public function testGetBuildsForGroup($c_empty, $c_unique, $c_single, $c_full)
	{
		$this->assertEmpty($c_empty->getBuildsForGroup(0));

		$this->assertCount(1, $c_unique->getBuildsForGroup(0));
		$this->assertCount(0, $c_unique->getBuildsForGroup(1));
		$this->assertEquals("G0B0", $c_unique->getBuildsForGroup(0)[0]->getName());

		$this->assertCount(4, $c_single->getBuildsForGroup(0));
		$this->assertCount(0, $c_single->getBuildsForGroup(1));
		$this->assertEquals("G0B0", $c_single->getBuildsForGroup(0)[0]->getName());
		$this->assertEquals("G0B1", $c_single->getBuildsForGroup(0)[1]->getName());
		$this->assertEquals("G0B2", $c_single->getBuildsForGroup(0)[2]->getName());
		$this->assertEquals("G0B3", $c_single->getBuildsForGroup(0)[3]->getName());

		$this->assertCount(4, $c_full->getBuildsForGroup(0));
		$this->assertCount(4, $c_full->getBuildsForGroup(1));
		$this->assertCount(0, $c_full->getBuildsForGroup(2));
		$this->assertEquals("G0B0", $c_full->getBuildsForGroup(0)[0]->getName());
		$this->assertEquals("G0B1", $c_full->getBuildsForGroup(0)[1]->getName());
		$this->assertEquals("G0B2", $c_full->getBuildsForGroup(0)[2]->getName());
		$this->assertEquals("G0B3", $c_full->getBuildsForGroup(0)[3]->getName());
		$this->assertEquals("G1B0", $c_full->getBuildsForGroup(1)[4]->getName());
		$this->assertEquals("G1B1", $c_full->getBuildsForGroup(1)[5]->getName());
		$this->assertEquals("G1B2", $c_full->getBuildsForGroup(1)[6]->getName());
		$this->assertEquals("G1B3", $c_full->getBuildsForGroup(1)[7]->getName());
	}

	/**
	 * @dataProvider allCompositionsProvider
	 */
	public function testGetBuild($c_empty, $c_unique, $c_single, $c_full)
	{
		$this->assertEquals("G0B0", $c_unique->getBuild(0)->getName());

		$this->assertEquals("G0B0", $c_single->getBuild(0)->getName());
		$this->assertEquals("G0B1", $c_single->getBuild(1)->getName());
		$this->assertEquals("G0B2", $c_single->getBuild(2)->getName());
		$this->assertEquals("G0B3", $c_single->getBuild(3)->getName());

		$this->assertEquals("G0B0", $c_full->getBuild(0)->getName());
		$this->assertEquals("G0B1", $c_full->getBuild(1)->getName());
		$this->assertEquals("G0B2", $c_full->getBuild(2)->getName());
		$this->assertEquals("G0B3", $c_full->getBuild(3)->getName());
		$this->assertEquals("G1B0", $c_full->getBuild(4)->getName());
		$this->assertEquals("G1B1", $c_full->getBuild(5)->getName());
		$this->assertEquals("G1B2", $c_full->getBuild(6)->getName());
		$this->assertEquals("G1B3", $c_full->getBuild(7)->getName());
	}

	/**
	 * @dataProvider emptyCompositionProvider
	 */
	public function testGetBuildEmpty($c_empty)
	{
		$this->expectException(\OutOfBoundsException::class);
		$build = $c_empty->getBuild(0);
	}

	/**
	 * @dataProvider singleBuildCompositionProvider
	 */
	public function testGetBuildUnique($c_unique)
	{
		$this->expectException(\OutOfBoundsException::class);
		$build = $c_unique->getBuild(1);
	}

	/**
	 * @dataProvider singleGroupCompositionProvider
	 */
	public function testGetBuildSingle($c_single)
	{
		$this->expectException(\OutOfBoundsException::class);
		$build = $c_single->getBuild(4);
	}

	/**
	 * @dataProvider twoGroupsCompositionProvider
	 */
	public function testGetBuildFull($c_full)
	{
		$this->expectException(\OutOfBoundsException::class);
		$build = $c_full->getBuild(8);
	}
}
