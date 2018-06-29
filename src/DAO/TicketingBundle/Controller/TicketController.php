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
		$visitor = new Visitor;
		$form = $this->get('form.factory')->create(VisitorType::class, $visitor);
		$ticket = new Ticket;
		$ticket->setDateResa(new \Datetime());

		$form = $this->get('form.factory')->create(TicketType::class, $ticket);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($ticket);
			$em->flush();

			//return $this->redirectToRoute('dao_ticketing_register', array('id' => $ticket->getId()));
			return $this->registerVisitorAction($request);
		}

		return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
	}

	public function registerVisitorAction (Request $request)
	{	

		$ticket = new Ticket();
		$req = $request->request->all();
		
    	$visitor = new Visitor();
		$form = $this->get('form.factory')->create(VisitorType::class, $visitor);
		var_dump($visitor);
		var_dump($req);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($visitor);
			$em->persist($req);
			$em->flush();

			//return $this->redirectToRoute('dao_ticketing_summery');
			return $this->registerSummeryAction($request);
			var_dump($request);
			
		}
		
		return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array(
				'form' => $form->createView(), 
				'mailVisiteur' => $req["dao_ticketingbundle_ticket"]["mailVisiteur"],
				'dateResa' => $req["dao_ticketingbundle_ticket"]["dateResa"],
				'ticketType' => $req["dao_ticketingbundle_ticket"]["ticketType"],
				'nbTickets' => $req["dao_ticketingbundle_ticket"]["nbTickets"],));

	}

	public function registerSummeryAction (Request $request)
	{
		$ticket = new Visitor();
		$req = $request->request->all();

		$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:index.html.twig');
		return new Response($content);
	}
}