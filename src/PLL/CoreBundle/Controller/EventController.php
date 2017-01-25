<?php

namespace PLL\CoreBundle\Controller;

use PLL\CoreBundle\Entity\Composition;
use PLL\UserBundle\Entity\Player;
use PLL\CoreBundle\Entity\Event;

use PLL\CoreBundle\Form\Type\EventType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    public function eventsAction()
    {
    	$events = $this->getUser()->getEvents();

    	return $this->render('PLLCoreBundle:Event:home.html.twig', array(
    		'events' => $events,
    	));
    }

    public function neweventAction(Request $request, $_locale)
    {
    	$event = new Event();
        $event->setDate(new \DateTime());
    	$form = $this->get('form.factory')->create(EventType::class, $event, array(
            'guild_id' => $this->getUser()->getId()
        ));

    	if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    		$this->getUser()->addEvent($event);
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($event);
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("event.message.created"));

    		return $this->redirectToRoute('pll_core_events', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Event:form.html.twig', array(
    		'form' => $form->createView()
    	));
    }

	/**
	 * @ParamConverter("event", options={"mapping": {"id": "id"}})
	 */
    public function editeventAction(Request $request, Event $event, $_locale)
    {
        if($this->getUser() !== $event->getGuild()) {
            throw new NotFoundHttpException('This event does not exist!');
        }
        
        $form = $this->get('form.factory')->create(EventType::class, $event, array(
            'guild_id' => $this->getUser()->getId()
        ));

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
    	    $em = $this->getDoctrine()->getManager();
    		$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("event.message.modified"));

    		return $this->redirectToRoute('pll_core_events', array('_locale' => $_locale));
    	}

    	return $this->render('PLLCoreBundle:Event:form.html.twig', array(
    		'form' => $form->createView()
    	));
    }

	/**
	 * @ParamConverter("event", options={"mapping": {"id": "id"}})
	 */
    public function deleteeventAction(Request $request, Event $event, $_locale)
    {
        if($this->getUser() !== $event->getGuild()) {
            throw new NotFoundHttpException('This event does not exist!');
        }

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        	$em = $this->getDoctrine()->getManager();
	    	$em->remove($event);
	    	$em->flush();

			$translator = $this->get('translator');
	    	$request->getSession()->getFlashBag()->add('notice', $translator->trans("event.message.deleted"));

	        return $this->redirectToRoute('pll_core_events', array('_locale' => $_locale));
	    }
	    
	    return $this->render('PLLCoreBundle:Event:delete.html.twig', array(
			'event' => $event,
			'form'   => $form->createView(),
	    ));
    }
}
