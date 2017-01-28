<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Category;
use PLL\CoreBundle\Entity\Project;
use PLL\CoreBundle\Entity\Contact;

use PLL\CoreBundle\Form\ContactType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
	public function welcomeAction()
    {
    	$securityContext = $this->container->get('security.authorization_checker');
		if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
    		return $this->redirectToRoute('pll_core_home');
		} else {
			return $this->redirectToRoute('pll_core_landing');
		}
    }

    public function landingAction()
	{
    	$securityContext = $this->container->get('security.authorization_checker');
		if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
    		return $this->redirectToRoute('pll_core_home');
		}

		$em = $this->getDoctrine()->getManager();

    	return $this->render('PLLCoreBundle::landing.html.twig', array(
    		'count_guilds' 		 => $this->getEntityCount($em, 'PLLUserBundle:Guild'),
    		'count_players' 	 => $this->getEntityCount($em, 'PLLCoreBundle:Player'),
    		'count_compositions' => $this->getEntityCount($em, 'PLLCoreBundle:Composition'),
    	));
	}

	private function getEntityCount($em, $entity)
	{
		return $em
			->createQueryBuilder()
			->select('count(a.id)')
			->from($entity, 'a')
			->getQuery()
			->getSingleScalarResult()
		;
	}
	
}
