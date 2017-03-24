<?php

namespace PLLCoreBundle\Tests\Analytics;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Composition;

use PLL\CoreBundle\Analytics\CompositionAnalytics;

class CompositionAnalyticsTest extends TestCase
{
	public function testMain()
	{
		$boss = array(
			"valegardian" => 0,
			"gorseval" => 1,
			"sabetha" => 2,
		);

		$c1 = new Composition();
		$c1->setBoss("boss.gorseval");

		$c2 = new Composition();
		$c2->setBoss("boss.sabetha");

		$c3 = new Composition();
		$c3->setBoss("boss.sabetha");

		$analytics = new CompositionAnalytics();
		$results = $analytics->run(array($c1, $c2, $c3));

		foreach ($boss as $name => $count) {
			$this->assertEquals($count, $results['boss.'.$name]);
		}
	}
}

?>