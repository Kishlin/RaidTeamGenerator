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
    	return $this->render('PLLCoreBundle::landing.html.twig');
	}
	
}
