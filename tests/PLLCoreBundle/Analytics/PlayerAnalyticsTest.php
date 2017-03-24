<?php

namespace PLLCoreBundle\Tests\Analytics;

use PHPUnit\Framework\TestCase;

use PLL\CoreBundle\Entity\Preference;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Build;

use PLL\CoreBundle\Analytics\PlayerAnalytics;

class PlayerAnalyticsTest extends TestCase
{
	public function setBuildId($build, $id)
	{
		$reflector = new \ReflectionProperty('PLL\CoreBundle\Entity\Build', 'id');
		$reflector->setAccessible(true);
		$reflector->setValue($build, $id);
		return $build;
	}

	public function mainProvider()
	{
		$p1 = new Player();
		$p2 = new Player();
		$p3 = new Player();

		$b1 = $this->setBuildId(new Build(), 0);
		$b2 = $this->setBuildId(new Build(), 1);
		$b3 = $this->setBuildId(new Build(), 2);

		$preferences = array(
			array($p1, $b1, 5),
			array($p2, $b1, 0),
			array($p3, $b1, 0),
			array($p1, $b2, 3),
			array($p2, $b2, 10),
			array($p3, $b2, 6),
			array($p1, $b3, 0),
			array($p2, $b3, 0),
			array($p3, $b3, 0),
		);

		foreach ($preferences as $pref) {
			$p = new Preference();
			$p->setPlayer($pref[0]);
			$p->setBuild($pref[1]);
			$p->setLevel($pref[2]);
		}

		$analytics = new PlayerAnalytics();

		$players = array($p1, $p2, $p3);
		$builds  = array($b1, $b2, $b3);

		return array(array($analytics, $players, $builds));
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testMain($analytics, $players, $builds)
	{
		$ret = $analytics->run($players, $builds);

		$this->assertEquals($ret[0]['total'], 1);
		$this->assertEquals($ret[0]['min'], 5);
		$this->assertEquals($ret[0]['max'], 5);
		$this->assertEquals($ret[0]['avg'], 5);

		$this->assertEquals($ret[1]['total'], 3);
		$this->assertEquals($ret[1]['min'], 3);
		$this->assertEquals($ret[1]['max'], 10);
		$this->assertEquals($ret[1]['avg'], 6.33);

		$this->assertEquals($ret[2]['total'], 0);
		$this->assertEquals($ret[2]['min'], 0);
		$this->assertEquals($ret[2]['max'], 0);
		$this->assertEquals($ret[2]['avg'], 0);
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testNoPlayers($analytics, $players, $builds)
	{
		$players = array();
		$ret = $analytics->run($players, $builds);

		foreach ($ret as $buildid => $stats) {
			$this->assertEquals($stats['total'], 0);
			$this->assertEquals($stats['min'], 0);
			$this->assertEquals($stats['max'], 0);
			$this->assertEquals($stats['avg'], 0);
		}
	}

	/**
	 * @dataProvider mainProvider
	 */
	public function testNoBuilds($analytics, $players, $builds)
	{
		$builds = array();
		$ret = $analytics->run($players, $builds);

		$this->assertCount(0, $ret);
	}
}

?>