<?php

namespace PLL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class GuildController extends Controller
{
	public function homeAction()
    {
    	return $this->render('PLLCoreBundle:Guild:home.html.twig');
    }	
}
