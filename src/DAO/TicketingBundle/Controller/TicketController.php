<?php

//src/DAO/TicketingBundle/Controller/TicketControler.php

namespace DAO\TicketingBundle\Controller;

use DAO\TicketingBundle\Entity\Ticket;
use DAO\TicketingBundle\Entity\Visitor;
use DAO\TicketingBundle\Entity\Rate;
use DAO\TicketingBundle\Form\TicketType;
use DAO\TicketingBundle\Form\VisitorType;
use DAO\TicketingBundle\Form\TicketRegisterType;
use DAO\TicketingBundle\Service\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use DAO\TicketingBundle\Mailer\Mailer;

class TicketController extends Controller
{
	static $i = 0;

	public function indexAction()
	{
		$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:index.html.twig');
		return new Response($content);
	}

	public function registerDateAction( Request $request)
	{
		
		$ticket = new Ticket;
		
		$form = $this->get('form.factory')->create(TicketType::class, $ticket);
		$form->handleRequest($request);

		$date = new \DateTime('now');
		
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$repository = $em->getRepository('DAOTicketingBundle:Ticket');
			$ticketCount = $repository->getTicketsCount($date);

			$date_valide = $ticket->getDateResa();

			$nbTickets = $ticket->getNbTickets();

			//$ticket->setResaCode("V".strtotime($date_valide->format('Y/m/d'))."Z".$nbTickets."louvre");

			$type_valide = $ticket->getTicketType();

			$ticketService = $this->container->get('dao_ticketing.ticketservice');
			$isValideDate = $ticketService->isValideDate($date_valide, $nbTickets, $ticketCount, $type_valide);
			
			if ($isValideDate == 5)
			{
				$this->addFlash("error", "Le quota de nombre de visiteurs par jour a été dépassé, merci de choisir un autre jour de visite");
				return $this->redirectToRoute('dao_ticketing_date');
			}
			elseif ($isValideDate == 2){
				$em->persist($ticket);
				$em->flush();

				$current = 1;
				
				return $this->redirectToRoute('dao_ticketing_register', array(
					'id' => $ticket->getId(),
					'nbTickets' => $ticket->getNbTickets(),
					'current' => $current,
					'ticket' => $ticket
				));
			}			
		}
		return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
	}

	public function modifyTicketAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

    	$nbTickets = $request->request->get('nbTicket');
    	$ticket->setNbTickets($nbTickets);

    	$dateResa = new \DateTime($request->request->get('dateResa'));
    	$ticket->setDateResa($dateResa);

    	$ticketType = $request->request->get('ticketType');
    	$ticket->setTicketType($ticketType);

		$em->persist($ticket);
		$em->flush(); 

		$current = 1;
		return $this->redirectToRoute('dao_ticketing_register', array(
					'id' => $ticket->getId(),
					'nbTickets' => $ticket->getNbTickets(),
					'current' => $current,
					'ticket' => $ticket
				));
	}

	public function deleteTicketAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

    	$em->remove($ticket); 
		$em->flush(); 

		return $this->redirectToRoute('dao_ticketing_home');
	}

	public function registerVisitorAction ($id, $nbTickets, $current, Request $request)
	{	
		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);
    	$dateResa = new \DateTime($request->request->get('dateResa'));
    	$ticket->setResaCode("V".strtotime($dateResa->format('Y/m/d'))."Z".$nbTickets."louvre".$id);
    	$em->persist($ticket);
		$em->flush(); 
		$visitor = new Visitor();			
		
		$form = $this->get('form.factory')->create(VisitorType::class, $visitor);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$visitor->setTicket($ticket);
			$age = $visitor->getAge();

			if ($age < 4 && $nbTickets ==1) {
				$this->addFlash("error", "Un enfant de moins de 4 ans doit impérativement être accompagné d'un adulte, cliquer sur le bouton modifier, pour ajouter au mlins 1 visiteur");
				return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array( 
					'form' => $form->createView(),
					'ticket' => $ticket,
				));
			}

			$reduced = $visitor->getReduced();	

			$ticketService = $this->container->get('dao_ticketing.ticketservice');
    		$priceType = $ticketService->getPrice($age, $reduced);		

			$visitor->setPrix($priceType);
			$em = $this->getDoctrine()->getManager();	
			$em->persist($visitor);
			$em->flush();
			
			if ($current < $nbTickets) {
				$current++;
				
				return $this->redirectToRoute('dao_ticketing_register', array( 
					'id' => $ticket->getId(),
					'nbTickets' => $ticket->getNbTickets(),
					'current' => $current,
					'ticket' => $ticket

					));
			}
			if($reduced){
				$this->addFlash("error", "Vous avez choisi un tarif réduit, lors de votre visite, merci de vous munir de votre carte d'étudiant, d'employé du musée, du service du Ministère de la Culture, ou de militaire");
			}
			return $this->redirectToRoute('dao_ticketing_summery', array(
						'id' => $ticket->getId(),
						'visitors' => $visitor->getTicket(),
						'ticket' => $ticket));					
		}
		
		return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array( 
				'form' => $form->createView(),
				'ticket' => $ticket,
				));
	}

	public function modifyVisitorAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
	    $ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

		$eManager = $this->getDoctrine()->getManager();	      
	    $visitors = $eManager->getRepository('DAOTicketingBundle:Visitor')
	      ->findBy(array('ticket' => $ticket->getId()))
	    ;
	    foreach ($visitors as $visitor){
	    	$nom = $request->request->get('nom');
	    	$visitor->setNom($nom);

	    	$prenom = $request->request->get('prenom');
	    	$visitor->setPrenom($prenom);

	    	$birthDate = new \DateTime($request->request->get('birthDate'));
	    	$visitor->setBirthDate($birthDate);

	    	$pays = $request->request->get('pays');
	    	$visitor->setPays($pays);
    	}
    	
    	$eManager->persist($visitor);
		$eManager->flush(); 

		return $this->redirectToRoute('dao_ticketing_summery', array(
						'id' => $id));
	}

	public function deleteVisitorAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);

    	$em->remove($visitor); 
		$em->flush(); 

		return $this->redirectToRoute('dao_ticketing_summery', array(
						'id' => $visitor->getTicket()->getId()));
	}

	public function registerSummeryAction ($id, Request $request)
	{

		$em = $this->getDoctrine()->getManager();
	    $ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

		$visitors = $this->getDoctrine()
	      ->getManager()
	      ->getRepository('DAOTicketingBundle:Visitor')
	      ->findBy(array('ticket' => $ticket->getId()))
	    ;

		if (!$visitors){
			return $this->redirectToRoute('dao_ticketing_date');
		}
	    foreach ($visitors as $visitor) {
	    	$prix[] = $visitor->getPrix();
	    }
    	$ticketService = $this->container->get('dao_ticketing.ticketservice');
    	$total = $ticketService->sumTotal($prix);

		return $this->render('DAOTicketingBundle:Ticket:recapitulatif.html.twig', array(
			'visitors' => $visitors,
			'total' => $total,
			'ticket' => $ticket,));
	}

	public function paymentIndexAction ($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

    	$visitors = $this->getDoctrine()
			->getManager()
			->getRepository('DAOTicketingBundle:Visitor')
			->findBy(array('ticket' => $ticket->getId()))
			;
		
		foreach ($visitors as $visitor) {
	    	$prix[] = $visitor->getPrix();
	    }
		$ticketService = $this->container->get('dao_ticketing.ticketservice');
    	$total = $ticketService->sumTotal($prix);
		
		return $this->render('DAOTicketingBundle:Payment:base.html.twig', array(
			'visitors' => $visitors,
			'id' => $ticket->getId(),
			'total' => $total,
			'ticket' => $ticket,));
	}

	public function paymentAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);
    	$visitors = $ticket->getVisitors();

        \Stripe\Stripe::setApiKey("sk_test_mlM2espcCKxhUmKCA266wduq");

        $token = $_POST['stripeToken'];

        try {
            $mailer = $this->container->get('dao_ticketing.mail'); 

        	$mailer->sendTicket($visitors, $ticket);

            return $this->redirectToRoute("dao_ticketing_confirming", array(
			'visitors' => $visitors ));

        }catch(\Stripe\Error\Card $e) {
            return $this->render('DAOTicketingBundle:Payment:base.html.twig', array(
			'visitors' => $visitors,
			'id' => $visitor->getId()));
		}
	}

	public function paymentConfirmingAction(Request $request)
	{
		return $this->render('DAOTicketingBundle:Payment:confirming.html.twig');
	}
}