<?php

namespace PLL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function homeAction()
    {
        return $this->render('PLLCoreBundle::admin.html.twig');
    }
}
