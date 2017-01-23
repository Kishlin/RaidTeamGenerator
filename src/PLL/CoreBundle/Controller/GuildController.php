<?php

namespace PLL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GuildController extends Controller
{
	public function homeAction()
    {
    	return $this->render('PLLCoreBundle:Guild:home.html.twig');
    }
}
