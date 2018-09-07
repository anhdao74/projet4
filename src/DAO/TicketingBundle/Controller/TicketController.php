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

			$ticket->setResaCode("V".strtotime($date_valide->format('Y/m/d'))."Z".$nbTickets."louvre");

			$type_valide = $ticket->getTicketType();

			$ticketService = $this->container->get('dao_ticketing.ticketservice');
			$isValideDate = $ticketService->isValideDate($date_valide, $nbTickets, $ticketCount, $type_valide);

			if ($isValideDate == 1){
				$this->addFlash("error","Nous fermons nos portes le 1er mai, le 1er novembre, le 25 décembre et tous les mardi");
				return $this->redirectToRoute('dao_ticketing_date', array('form' => $form->createView()));
			}elseif ($isValideDate == 3) {
				echo "Vous ne pouvez pas réserver pour les jours passés";
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
			}elseif ($isValideDate == 4) {
				echo "Vous ne pouvez pas réserver un ticket journée pour aujourd'hui, vous pouvez choisir un billet demi-journéé ou un autre jour.";
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
			}elseif ($isValideDate == 5) {
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
				echo "Le quota de nombre de visiteurs par jour a été dépassé, merci de choisir un autre jour de visite";
			}elseif ($isValideDate == 2){
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
		//var_dump($isValideDate);
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
		$visitor = new Visitor();			
		
		$form = $this->get('form.factory')->create(VisitorType::class, $visitor);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$visitor->setTicket($ticket);
			$age = $visitor->getAge();
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
    	$visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);

    	$nom = $request->request->get('nom');
    	$visitor->setNom($nom);

    	$prenom = $request->request->get('prenom');
    	$visitor->setPrenom($prenom);

    	$birthDate = new \DateTime($request->request->get('birthDate'));
    	$visitor->setBirthDate($birthDate);

    	$pays = $request->request->get('pays');
    	$visitor->setPays($pays);

    	$em->persist($visitor);
		$em->flush(); 

		$id = $visitor->getTicket();

		return $this->redirectToRoute('dao_ticketing_summery', array(
						'id' => $id));
	}

	public function deleteVisitorAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);

    	$em->remove($visitor); 
		$em->flush(); 

		if ($visitor->getId() == 0){
			return $this->redirectToRoute('dao_ticketing_date');
		}
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

    	//$ticket = $visitor->getTicket();
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
			'visitors' => $visitors
			/*'id' => $visitor->getId(),
			'ticket' => $ticket*/));

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