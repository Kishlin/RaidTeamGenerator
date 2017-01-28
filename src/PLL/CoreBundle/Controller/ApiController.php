<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\ApiKey;
use PLL\CoreBundle\Entity\Event;
use PLL\UserBundle\Entity\Guild;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    private function returnErrorResponse($message)
    {
        return new JsonResponse(array('error' => $message));
    }

    private function returnApikeyErrorResponse()
    {
        return $this->returnErrorResponse('apikey');
    }

    public function buildAction($apikey)
    {
    	$ret = $this->getDoctrine()->getRepository('PLLCoreBundle:Apikey')->getGuildWithKey($apikey);

    	if(count($ret) !== 1) {
    		return $this->returnApikeyErrorResponse();
    	} else {
    		$builds = array();
    		$guild = $ret[0]->getGuild();
    		foreach ($guild->getBuilds() as $build) {
    			$builds[$build->getId()] = array(
    				'id'     => $build->getId(),
    				'name'   => $build->getName(),
    				'img'    => $build->getImg(),
    				'imgsub' => $build->getImgsub(),
    			);
    		}
    		return new JsonResponse($builds);
    	}
    }

    public function playerAction($apikey)
    {
    	$ret = $this->getDoctrine()->getRepository('PLLCoreBundle:Apikey')->getGuildWithKey($apikey);

    	if(count($ret) !== 1) {
    		return $this->returnApikeyErrorResponse();
    	} else {
    		$players = array();
    		$guild = $ret[0]->getGuild();
    		foreach ($guild->getPlayers() as $player) {
    			$preferences = array();
    			foreach ($player->getPreferences() as $pref) {
    				$preferences[$pref->getBuild()->getId()] = array(
    					'build' => $pref->getBuild()->getId(),
    					'level' => $pref->getLevel(),
    				);
    			}

    			$players[$player->getId()] = array(
    				'id'     	  => $player->getId(),
    				'name'   	  => $player->getName(),
    				'preferences' => $preferences,
    			);
    		}
    		return new JsonResponse($players);
    	}
    }

    public function compositionAction($apikey)
    {
    	$ret = $this->getDoctrine()->getRepository('PLLCoreBundle:Apikey')->getGuildWithKey($apikey);

    	if(count($ret) !== 1) {
    		return $this->returnApikeyErrorResponse();
    	} else {
    		$compositions = array();
    		$guild = $ret[0]->getGuild();
    		foreach ($guild->getCompositions() as $composition) {
    			$builds = array();
    			foreach ($composition->getCompositionbuilds() as $cb) {
    				$builds[$cb->getId()] = array(
    					'build' => $cb->getBuild()->getId(),
    					'group' => $cb->getGroupindex(),
    				);
    			}

    			$compositions[$composition->getId()] = array(
    				'id'     	  => $composition->getId(),
    				'name'   	  => $composition->getName(),
    				'size'		  => $composition->getSize(),
    				'boss'		  => $composition->getBoss(),
    				'groupscount' => $composition->getGroupscount(),
    				'builds' 	  => $builds,
    			);
    		}
    		return new JsonResponse($compositions);
    	}
    }

    public function eventAction($apikey)
    {
    	$ret = $this->getDoctrine()->getRepository('PLLCoreBundle:Apikey')->getGuildWithKey($apikey);

    	if(count($ret) !== 1) {
    		return $this->returnApikeyErrorResponse();
    	} else {
    		$events = array();
    		$guild = $ret[0]->getGuild();
    		foreach ($guild->getEvents() as $event) {
    			$players = array();
    			foreach ($event->getPlayers() as $p) {
    				$players[] = $p->getId();
    			}
    			$compositions = array();
    			foreach ($event->getCompositions() as $c) {
    				$compositions[] = $c->getId();
    			}

    			$events[$event->getId()] = array(
    				'id'     	   => $event->getId(),
    				'name'   	   => $event->getName(),
    				'date'		   => $event->getDate(),
    				'time'		   => $event->getTime(),
    				'players' 	   => join(', ', $players),
    				'compositions' => join(', ', $compositions),
    			);
    		}
    		return new JsonResponse($events);
    	}
    }

    public function buildCPAction($apikey, $compositions, $players)
    {
		$validator = $builder = $guild = null;
        $comps = $ps = array();

        $em = $this->getDoctrine()->getManager();
        
        $ret = $em->getRepository('PLLCoreBundle:Apikey')->getGuildWithKey($apikey);

        if(count($ret) !== 1) {
            return $this->returnApikeyErrorResponse();
        }

        $guild = $ret[0]->getGuild();

        $players_repo = $em->getRepository("PLLCoreBundle:Player");
        $compositions_repo = $em->getRepository("PLLCoreBundle:Composition");

        foreach (explode('-', $compositions) as $id) {
            $c = $compositions_repo->find((int)$id);
            if($c === null) {
                return $this->returnErrorResponse("404.comp");
            } else {
                $comps[] = $c;
            }
        }

        foreach (explode('-', $players) as $id) {
            $p = $players_repo->find((int)$id);
            if($p === null) {
                return $this->returnErrorResponse("404.player");
            } else {
                $ps[] = $p;
            }
        }

        $validator = $this->get('pll_core.team.validator');

        $message = $validator
            ->setupWithPlayersAndCompositions($ps, $comps)
            ->validate($guild)
        ;

        return $this->getBuilderResponse($validator, $message);
    }

	/**
	 * @ParamConverter("event", options={"mapping": {"id": "id"}})
	 */
    public function buildEAction($apikey, Event $event)
    {	
    	$ret = $this->getDoctrine()->getRepository('PLLCoreBundle:Apikey')->getGuildWithKey($apikey);

    	if(count($ret) !== 1) {
    		return $this->returnApikeyErrorResponse();
    	}

    	$guild = $ret[0]->getGuild();

    	$validator = $this->get('pll_core.team.validator');

    	$message = $validator
    		->setupWithEvent($event)
    		->validate($guild)
    	;

    	return $this->getBuilderResponse($validator, $message);
    }

    private function getBuilderResponse($validator, $message)
    {
        if($message !== null) {
            return new JsonResponse(array('error' => 'validator', 'message' => $message));
        }

        $builder = $this->get('pll_core.team.builder');

        $messages = $builder
            ->setLogger($this->get('logger'))
            ->setPlayers($validator->getPlayers())
            ->setCompositions($validator->getCompositions())
            ->build()
        ;

        $teams = array();
        foreach ($builder->getTeams() as $team) {
            $teams[$team->getComposition()->getId()] = $team->toArray();
        }

        $response = array('messages' => $messages, 'teams' => $teams);

        return new JsonResponse($response);
    }
}
