<?php 

namespace PLL\CoreBundle\Analytics;

use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\Player;

class PlayerAnalytics
{
        public function __construct()
        {

        }

        public function run($players, $builds)
        {
                $analytics = array();
                foreach ($builds as $build) {
                        $prefs = array();
                        foreach ($players as $player) {
                                $pref = $player->getPreferenceForBuild($build)->getLevel();
                                if($pref !== 0) {
                                        $prefs[] = $pref;
                                }
                        }

                        $analytics[$build->getId()] = array();
                        $analytics[$build->getId()]['build'] = $build;
                        $analytics[$build->getId()]['total'] = count(array_filter($prefs, function($value) {return $value !== 0;}));
                        $analytics[$build->getId()]['min'] = (count($prefs) === 0) ? 0 : min($prefs);
                        $analytics[$build->getId()]['max'] = (count($prefs) === 0) ? 0 : max($prefs);
                        $analytics[$build->getId()]['avg'] = (count($prefs) === 0) ? 0 : round(array_sum($prefs)/count($prefs), 2); 
                }

                return $analytics;
        }
}

?>