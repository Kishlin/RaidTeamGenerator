<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Apikey;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GuildController extends Controller
{
	public function homeAction(Request $request)
    {
    	$guild = $this->getUser();
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        	$apikey = new Apikey();
        	$apikey->setApikey(md5($guild->getUsername() . '~' . time()));
        	$guild->setApikey($apikey);
        	$apikey->setGuild($guild);
        	
        	$em = $this->getDoctrine()->getManager();
        	$em->persist($apikey);
        	$em->flush();
        }

        $composition_analytics = $this->get('pll_core.analytics.composition');
        $player_analytics      = $this->get('pll_core.analytics.player');

    	return $this->render('PLLCoreBundle:Home:home.html.twig', array(
            'composition_analytics' => $composition_analytics->run($guild->getCompositions()),
            'player_analytics'      => $player_analytics->run($guild->getPlayers(), $guild->getBuilds()),
			'form'   => $form->createView(),
			'apikey' => $guild->getApikey(),
    	));
    }

	public function deleteAction(Request $request, $_locale)
    {
    	$guild = $this->getUser();
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        	$apikey = $guild->getApikey();
        	$guild->setApikey(null);

        	$em = $this->getDoctrine()->getManager();
        	$em->remove($apikey);
        	$em->flush();

    		return $this->redirectToRoute('pll_core_home', array('_locale' => $_locale));
        }

    	return $this->render('PLLCoreBundle:Home:delete.html.twig', array(
			'form'   => $form->createView(),
			'apikey' => $guild->getApikey(),
    	));
    }
}
