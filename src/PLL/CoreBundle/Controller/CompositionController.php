<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Build;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\CompositionBuild;
use PLL\UserBundle\Entity\Guild;

use PLL\CoreBundle\Form\Type\CompositionType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CompositionController extends Controller
{
    public function compositionsAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        $repo = $this->getDoctrine()->getRepository('PLLCoreBundle:Composition');
    	$compositions = $repo->getCompositionsFull($this->getUser()->getId());

    	return $this->render('PLLCoreBundle:Composition:home.html.twig', array(
    		'compositions' => $compositions,
    	));
    }

    public function newcompositionAction(Request $request, $_locale)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        $builds = $this->getUser()->getBuilds();
        $composition = new Composition();

    	$form = $this->get('form.factory')->create(CompositionType::class, $composition);

    	if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $composition = $this->handleForm($request, $composition, $builds);
            
    		$em = $this->getDoctrine()->getManager();
            $this->getUser()->addComposition($composition);
    		$em->persist($composition);
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("composition.message.created"));

    		return $this->redirectToRoute('pll_core_compositions', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Composition:form.html.twig', array(
            'form'        => $form->createView(),
            'composition' => $composition,
            'builds'      => $builds,
    	));
    }

	/**
	 * @ParamConverter("composition", options={"mapping": {"id": "id"}})
	 */
    public function editcompositionAction(Request $request, Composition $composition, $_locale)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        if($this->getUser() !== $composition->getGuild()) {
            throw new NotFoundHttpException('This composition does not exist!');
        }
        
        $builds = $this->getUser()->getBuilds();
        $form = $this->get('form.factory')->create(CompositionType::class, $composition);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    	    $em = $this->getDoctrine()->getManager();
            foreach ($composition->getCompositionbuilds() as $compositionbuild) {
                $em->remove($compositionbuild);
                $composition->removeCompositionbuild($compositionbuild);
            }
            $composition = $this->handleForm($request, $composition, $builds);
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("composition.message.modified"));

    		return $this->redirectToRoute('pll_core_compositions', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Composition:form.html.twig', array(
    		'form'        => $form->createView(),
            'composition' => $composition,
            'builds'      => $builds,
    	));
    }

	/**
	 * @ParamConverter("composition", options={"mapping": {"id": "id"}})
	 */
    public function deletecompositionAction(Request $request, Composition $composition, $_locale)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        if($this->getUser() !== $composition->getGuild()) {
            throw new NotFoundHttpException('This composition does not exist!');
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        	$em = $this->getDoctrine()->getManager();
	    	$em->remove($composition);
	    	$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("composition.message.deleted"));

	        return $this->redirectToRoute('pll_core_compositions', array('_locale' => $_locale));
	    }
	    
	    return $this->render('PLLCoreBundle:Composition:delete.html.twig', array(
			'composition' => $composition,
			'form'        => $form->createView(),
	    ));
    }

    private function handleForm(Request $request, Composition $composition, $builds) 
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('pll_core_landing');
        }
        
        $size        = 0;
        $group_index = 0;
        $nb_groups   = $request->request->get('group-index', 0);

        for ($i=0; $i <= $nb_groups; $i++) { 
            $group_details = $request->request->get('group-'.$i, '');
            if($group_details !== '') {
                $build_ids = explode(',', $group_details);
                foreach ($build_ids as $build_id) {
                    $build = $builds->filter(
                        function($b) use($build_id) {
                            return $b->getId() === (int)$build_id;
                        }
                    )->first();
                    if(sizeof($build) !== 0) {
                        $size++;
                        $compositionbuild = new CompositionBuild();
                        $compositionbuild->setGroupindex($group_index);
                        $compositionbuild->setBuild($build);
                        $composition->addCompositionbuild($compositionbuild);
                    }
                }
                $group_index++;
            }
        }

        return $composition
            ->setSize($size)
            ->setGroupscount($group_index)
        ;
    }
}
