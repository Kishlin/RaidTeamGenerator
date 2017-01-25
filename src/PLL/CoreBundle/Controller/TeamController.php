<?php

namespace PLL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

use PLL\CoreBundle\Form\Type\TeamPlayersCompositionsType;
use PLL\CoreBundle\Form\Type\TeamEventsType;

use PLL\CoreBundle\Entity\Event;

class TeamController extends Controller
{
	public function teamAction(Request $request)
    {
    	$message = '';

    	$formplayerscompositions = $this->get('form.factory')->create(TeamPlayersCompositionsType::class, array(), array(
            'guild_id' => $this->getUser()->getId()
        ));
    	$formevents = $this->get('form.factory')->create(TeamEventsType::class, array(), array(
            'guild_id' => $this->getUser()->getId()
        ));

    	// Team Builder by event
    	if ($request->isMethod('POST')) {
    		$validator = $this->get('pll_core.team.validator');

    		if ($formevents->handleRequest($request)->isValid()) {
	    		$data = $formevents->getData();
	    		$event = $data['event'];
	    		$validator->setupWithEvent($event);
    		} else if ($formplayerscompositions->handleRequest($request)->isValid()) {
	    		$data = $formplayerscompositions->getData();
	    		$compositions = $data['compositions'];
	    		$players      = $data['players'];
				$validator->setupWithPlayersAndCompositions($players, $compositions);
    		}

    		$message = $validator->validate($this->getUser());
    	}

    	return $this->render('PLLCoreBundle:Team:home.html.twig', array(
    		'formplayerscompositions' => $formplayerscompositions->createView(),
    		'formevents'		      => $formevents		     ->createView(),
    		'message'			 	  => $message,
    	));
    }
	
}