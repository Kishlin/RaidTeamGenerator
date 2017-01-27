<?php 

namespace PLL\CoreBundle\Analytics;

use PLL\CoreBundle\Entity\Composition;

class CompositionAnalytics
{
	public function __construct()
	{

	}

	public function run($compositions)
	{
                $boss = array("valegardian", "gorseval", "sabetha", "slothasor", "camp", "matthias", "escort", "keepconstruct", "xera");

                $counts = array();
                foreach ($boss as $b) {
                	$counts["boss.".$b] = 0;
                }

                foreach ($compositions as $composition) {
                	$boss = $composition->getBoss();
                	$counts[$boss]++;
                }

                return $counts;
	}
}

 ?>