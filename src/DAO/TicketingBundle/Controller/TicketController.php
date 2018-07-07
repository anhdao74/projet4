<?php

//src/DAO/TicketingBundle/Controller/TicketControler.php

namespace DAO\TicketingBundle\Controller;

use DAO\TicketingBundle\Entity\Ticket;
use DAO\TicketingBundle\Entity\Visitor;
use DAO\TicketingBundle\Form\TicketType;
use DAO\TicketingBundle\Form\VisitorType;
use DAO\TicketingBundle\Form\TicketRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
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
		$form->handleRequest($request);


		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($ticket);

			$em->flush();


			return $this->redirectToRoute('dao_ticketing_register', array(
				'id' => $ticket->getId(),
				'nbTickets' => $ticket->getNbTickets()
			));
			//return $this->registerVisitorAction($request, array('id' => $ticket->getId()));
		}

		return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
	}

	public function registerVisitorAction ($id, $nbTickets, Request $request)
	{	

		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);
		//$ticket = new Ticket;
		//$req = $request->request->all();
		//var_dump($ticket);
		
		//for($i=0; $i < $nbTickets; $i++) { 
    		$this->visitors = $ticket->getNbTickets();
    		$visitor = new Visitor();

    		//$originalVisitors = new ArrayCollection();

			//foreach ($ticket->getNbTickets() as $visitor) {
				//$originalVisitors->add($visitor);
			//}
    		
			$form = $this->get('form.factory')->create(VisitorType::class, $visitor);//}
		//var_dump($form->handleRequest($request)->isValid());
		//var_dump($req);
		//var_dump($form->getErrors());
		
			var_dump($visitor);
		//var_dump($request->isMethod('POST'));
		//var_dump($form->handleRequest($request)->isValid());
		//
		
		//$id = $request->query->get('id');

		// on lie les visiteurs à la commande
            //foreach($this->visitors as $visitor) {
                //$visitor->setTicketOrder($order);
                //$this->em->persist($visitor);
            //}
            // enregistrement en base de données
            //$this->em->persist($order);
            //$this->em->flush();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
		{
			$visitor->setTicket($ticket);
			//$groupes = $form['$i']->getData()["groupes"];
			$em = $this->getDoctrine()->getManager();	
			//foreach ($groupes as $groupe) {
				$em->persist($visitor);//}
			$em->flush();

			return $this->redirectToRoute('dao_ticketing_summery');
			//return $this->registerSummeryAction($request);
			
		}
		return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array(
				'form' => $form->createView(), 
				'ticket' => $ticket,
				));
	}

	public function registerSummeryAction (Request $request)
	{
		
		$req = $request->request->all();

	//var_dump($req);

		$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:index.html.twig');
		return new Response($content);
	}
}