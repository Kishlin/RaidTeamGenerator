<?php

namespace PLLCoreBundle\Tests\Service;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Factory\BuildFactory;

class BuildFactoryTest extends TestCase
{
	public function testFactory()
	{
		$factory = new BuildFactory();
		
		$name = "Build Name";
		$img = "Img";
		$sub = "Imgsub";

		$build = $factory->createBuild($name, $img, $sub);

		$this->assertEquals($build->getName(), $name);
		$this->assertEquals($build->getImg(), $img);
		$this->assertEquals($build->getImgsub(), $sub);
	}
}

?>