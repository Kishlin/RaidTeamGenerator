<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Preference;
use PLL\CoreBundle\Entity\Build;
use PLL\UserBundle\Entity\Guild;

use PLL\CoreBundle\Form\Type\BuildType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BuildController extends Controller
{
    public function buildAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
    	$builds = $this->getUser()->getBuilds();

    	return $this->render('PLLCoreBundle:Build:home.html.twig', array(
    		'builds' => $builds,
    	));
    }

    public function newbuildAction(Request $request, $_locale)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
    	$build = new Build();
    	$form = $this->get('form.factory')->create(BuildType::class, $build);

    	if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            foreach ($this->getUser()->getPlayers() as $player) {
                $preference = new Preference();
                $preference->setLevel(0);
                $preference->setPlayer($player);
                $preference->setBuild($build);
            }

    		$this->getUser()->addBuild($build);
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($build);
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("build.message.created"));

    		return $this->redirectToRoute('pll_core_builds', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Build:new.html.twig', array(
    		'form' => $form->createView()
    	));
    }

	/**
	 * @ParamConverter("build", options={"mapping": {"id": "id"}})
	 */
    public function editbuildAction(Request $request, Build $build, $_locale)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        if($this->getUser() !== $build->getGuild()) {
            throw new NotFoundHttpException('This build does not exist!');
        }
        
        $form = $this->get('form.factory')->create(BuildType::class, $build);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    	    $em = $this->getDoctrine()->getManager();
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("build.message.modified"));

    		return $this->redirectToRoute('pll_core_builds', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Build:edit.html.twig', array(
    		'form' => $form->createView()
    	));
    }

	/**
	 * @ParamConverter("build", options={"mapping": {"id": "id"}})
	 */
    public function deletebuildAction(Request $request, Build $build, $_locale)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        if($this->getUser() !== $build->getGuild()) {
            throw new NotFoundHttpException('This build does not exist!');
        }

        $params = array('build' => $build);

        if($build->getCompositionbuilds()->count() === 0) {
            $form = $this->get('form.factory')->create();

            if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($build);
                $em->flush();

                $translator = $this->get('translator');
                $request->getSession()->getFlashBag()->add('notice', $translator->trans("build.message.deleted"));

                return $this->redirectToRoute('pll_core_builds', array('_locale' => $_locale));
            }

            $params['form'] = $form->createView();
        }
	    
	    return $this->render('PLLCoreBundle:Build:delete.html.twig', $params);
    }

    public function adddefaultAction(Request $request, $_locale) 
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        $form = $this->get('form.factory')->create();
        $defaultbuilds = $this->get('pll_core.defaultbuilds')->getDefaultBuilds();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($defaultbuilds as $build) {
                foreach ($this->getUser()->getPlayers() as $player) {
                    $preference = new Preference();
                    $preference->setLevel(0);
                    $preference->setPlayer($player);
                    $preference->setBuild($build);
                }
            
                $this->getUser()->addBuild($build);
                $em->persist($build);
            }

            $em->flush();

            $translator = $this->get('translator');
            $request->getSession()->getFlashBag()->add('notice', $translator->trans("build.message.defaultbuildsadded"));

            return $this->redirectToRoute('pll_core_builds', array('_locale' => $_locale));
        }
        
        return $this->render('PLLCoreBundle:Build:adddefault.html.twig', array(
            'builds' => $defaultbuilds,
            'form'   => $form->createView(),
        ));
    }
}
