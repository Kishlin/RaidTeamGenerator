<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Build;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Composition;
use PLL\CoreBundle\Entity\CompositionGroup;
use PLL\CoreBundle\Entity\CompositionGroupBuild;
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
    	$compositions = $this->getUser()->getCompositions();

    	return $this->render('PLLCoreBundle:Composition:home.html.twig', array(
    		'compositions' => $compositions,
    	));
    }

    public function newcompositionAction(Request $request, $_locale)
    {
        $builds = $this->getUser()->getBuilds();
        $composition = new Composition();
    	$form = $this->get('form.factory')->create(CompositionType::class, $composition);

    	if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $nb_groups = $request->request->get('group-index', 0);

            for ($i=0; $i <= $nb_groups; $i++) { 
                $group_details = $request->request->get('group-'.$i, '');
                if($group_details !== '') {
                    $group = new CompositionGroup();
                    $build_ids = explode(',', $group_details);
                    foreach ($build_ids as $build_id) {
                        foreach ($builds as $build) {
                            if($build->getId() == $build_id) {
                                $group->addBuild($build);
                                break;
                            }
                        }
                    }
                    $composition->addGroup($group);
                }
            }
            
    		$em = $this->getDoctrine()->getManager();
            $this->getUser()->addComposition($composition);
    		$em->persist($composition);
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("composition.message.created"));

    		return $this->redirectToRoute('pll_core_compositions', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Composition:new.html.twig', array(
    		'form'   => $form->createView(),
            'builds' => $builds
    	));
    }

	/**
	 * @ParamConverter("build", options={"mapping": {"id": "id"}})
	 */
    public function editbuildAction(Request $request, Build $build, $_locale)
    {
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
	 * @ParamConverter("composition", options={"mapping": {"id": "id"}})
	 */
    public function deletecompositionAction(Request $request, Composition $composition, $_locale)
    {
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
}
