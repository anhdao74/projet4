<?php

//src/DAO/TicketingBundle/Controller/TicketControler.php

namespace DAO\TicketingBundle\Controller;

use DAO\TicketingBundle\Entity\Ticket;
use DAO\TicketingBundle\Entity\Visitor;
use DAO\TicketingBundle\Form\TicketType;
use DAO\TicketingBundle\Form\VisitorType;
use DAO\TicketingBundle\Form\TicketRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
	public function indexAction()
	{
		$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:index.html.twig');
		return new Response($content);
	}

	public function registerDateAction( Request $request)
	{
		
		$ticket = new Ticket;
		$ticket->setDateResa(new \Datetime());
		$form = $this->get('form.factory')->create(TicketType::class, $ticket);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($ticket);
			$em->flush();

			return $this->redirectToRoute('dao_ticketing_register', array('id' => $ticket->getId(), 'mailVisiteur' => $ticket->getMailVisiteur()));
		}

		return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
	}

	public function registerVisitorAction (Request $request)
	{	
		$ticket = new Ticket();
		$mailVisiteur = $request->query->get('mailVisiteur');

    	$visitor = new Visitor();
		
		$form = $this->get('form.factory')->create(VisitorType::class, $visitor);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($visitor);
			$em->flush();

			return $this->redirectToRoute('dao_ticketing_summery');
		}

		return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array(
				'form' => $form->createView(), 
				'mailVisiteur' => $mailVisiteur,));
	}

	public function registerSummeryAction (Request $request)
	{
		$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:index.html.twig');
		return new Response($content);
	}
}