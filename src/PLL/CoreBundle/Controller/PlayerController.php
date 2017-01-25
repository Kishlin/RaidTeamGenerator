<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Preference;
use PLL\CoreBundle\Entity\Player;
use PLL\CoreBundle\Entity\Build;
use PLL\UserBundle\Entity\Guild;

use PLL\CoreBundle\Form\Type\PlayerType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends Controller
{
    public function playersAction()
    {
        $guild_id = $this->getUser()->getId();
        $repo_p   = $this  ->getDoctrine()->getRepository('PLLCoreBundle:Player');
        $players  = $repo_p->getPlayersForGuild($guild_id);
        $repo_b   = $this  ->getDoctrine()->getRepository('PLLCoreBundle:Build');
        $builds   = $repo_b->getBuildsWithPreferences($guild_id);
        
    	return $this->render('PLLCoreBundle:Player:home.html.twig', array(
    		'players' => $players,
            'builds'  => $builds
    	));
    }

    public function newplayerAction(Request $request, $_locale)
    {
    	$player = new Player();

        $builds = $this->getUser()->getBuilds();

    	$form = $this->get('form.factory')->create(PlayerType::class, $player);

    	if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    		$em = $this->getDoctrine()->getManager();
            foreach ($builds as $build) {
                $pref = (int)$request->request->get('pref-build-'.$build->getId(), 0);
                $pref = ($pref >= 0 || $pref <= 10) ? $pref : 0;
                $preference = new Preference();
                $preference->setLevel($pref);
                $preference->setPlayer($player);
                $preference->setBuild($build);
            }
            $this->getUser()->addPlayer($player);
    		$em->persist($player);
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("player.message.created"));

    		return $this->redirectToRoute('pll_core_players', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Player:new.html.twig', array(
    		'form'   => $form->createView(),
            'player' => $player,
            'builds' => $builds
    	));
    }

    /**
     * @ParamConverter("player", options={"mapping": {"id": "id"}})
     */
    public function editplayerAction(Request $request, Player $player, $_locale)
    {
        if($this->getUser() !== $player->getGuild()) {
            throw new NotFoundHttpException('This player does not exist!');
        }

        $builds = $this->getUser()->getBuilds();
        
        $form = $this->get('form.factory')->create(PlayerType::class, $player);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    	    $em = $this->getDoctrine()->getManager();
            foreach ($builds as $build) {
                $pref = (int)$request->request->get('pref-build-'.$build->getId(), 0);
                $pref = ($pref >= 0 || $pref <= 10) ? $pref : 0;
                $player->setPreferenceForBuild($build, $pref);
            }
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("player.message.modified"));

    		return $this->redirectToRoute('pll_core_players', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Player:edit.html.twig', array(
            'form'   => $form->createView(),
            'player' => $player,
            'builds' => $builds
        ));
    }

	/**
	 * @ParamConverter("player", options={"mapping": {"id": "id"}})
	 */
    public function deleteplayerAction(Request $request, Player $player, $_locale)
    {
        if($this->getUser() !== $player->getGuild()) {
            throw new NotFoundHttpException('This player does not exist!');
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        	$em = $this->getDoctrine()->getManager();
	    	$em->remove($player);
	    	$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("player.message.deleted"));

	        return $this->redirectToRoute('pll_core_players', array('_locale' => $_locale));
	    }
	    
	    return $this->render('PLLCoreBundle:Player:delete.html.twig', array(
			'player' => $player,
			'form'   => $form->createView(),
	    ));
    }
}
