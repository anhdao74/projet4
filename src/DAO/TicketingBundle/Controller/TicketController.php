<?php

//src/DAO/TicketingBundle/Controller/TicketControler.php

namespace DAO\TicketingBundle\Controller;

use DAO\TicketingBundle\Entity\Ticket;
use DAO\TicketingBundle\Entity\Visitor;
use DAO\TicketingBundle\Entity\Rate;
use DAO\TicketingBundle\Form\TicketType;
use DAO\TicketingBundle\Form\VisitorType;
use DAO\TicketingBundle\Form\TicketRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
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
		$ticket->setDateResa(new \DateTime());

		$form = $this->get('form.factory')->create(TicketType::class, $ticket);
		$form->handleRequest($request);

		
		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$code1 = $ticket->getId();
			$code2 = $ticket->getDateResa();
			
			$ticket->setResaCode('louvre6') ;
			$em->persist($ticket);

			$em->flush();


			return $this->redirectToRoute('dao_ticketing_register', array(
				'id' => $ticket->getId(),
				'nbTickets' => $ticket->getNbTickets(),
				'ticket' => $ticket
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
		$req = $request->request->all();
		//var_dump($req);		
    		$visitor = new Visitor();			
    		
			$form = $this->get('form.factory')->create(VisitorType::class, $visitor);

				if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
				{
					$visitor->setTicket($ticket);
					//$groupes = $form['$i']->getData()["groupes"];
					$age = $visitor->getAge();
					$reduced = $visitor->getReduced();

					//Détermine le tarif du billet
					if($age < 4)
					{
						$priceType = '0';
					}
						elseif($age >= 4 && $age < 12)
						{
							$priceType = '8';
						}
						elseif ($age >=60) 
						{
							$priceType = '12';
						}
					else
					{
						$priceType = '16';
					}
					if($reduced === true)
					{
						$priceType= ($priceType-10);
					}

					$visitor->setPrix($priceType);

					$em = $this->getDoctrine()->getManager();	
					//foreach ($groupes as $groupe) {
					$em->persist($visitor);//}
					$em->flush();
					self::$i++;

					/*incrementer index statique*/
					/*if (self::$i < $nbTickets)
					{
						return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array( 
							'form' => $form->createView(),
							'ticket' => $ticket,
							));
					}*/
										//return $this->registerSummeryAction($request);

					//$this->request->getSession()->set('visitor', $visitor);

					return $this->redirectToRoute('dao_ticketing_summery', array(
								'id' => $visitor->getId(),
								'ticket' => $ticket));					
				}
		
		return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array( 
				'form' => $form->createView(),
				'ticket' => $ticket,
				));
	}

	public function registerSummeryAction ($id, Request $request)
	{
	$em = $this->getDoctrine()->getManager();
    $visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);

    $em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);
	//var_dump($visitor);
	$req = $request->request->all();

		//$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:recapitulatif.html.twig');
		return $this->render('DAOTicketingBundle:Ticket:recapitulatif.html.twig', array(
			'visitor' => $visitor,
			'id' => $visitor->getId(),
			'ticket' => $ticket,));
	}

	public function paymentIndexAction ($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);

    	$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

    	$req = $request->request->all();

		return $this->render('DAOTicketingBundle:Payment:base.html.twig', array(
			'visitor' => $visitor,
			'id' => $visitor->getId(),
			'ticket' => $ticket,));
	}

	public function paymentAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
    	$visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);
    	$ticket_id = $visitor->getTicket();

    	$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id = $ticket_id);

        \Stripe\Stripe::setApiKey("sk_test_mlM2espcCKxhUmKCA266wduq");

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create a charge: this will charge the user's card
        try 
        {
            /*$charge = \Stripe\Charge::create(array(
                "amount" => 999, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - Billeterie du Louvre"
            ));*/
            $this->addFlash("success","Bravo ça marche !");
            $mailer = $this->container->get('dao_ticketing.mail'); 

        	$mailer->sendTicket($visitor, $ticket);

            return $this->redirectToRoute("dao_ticketing_confirming", array(
			'visitor' => $visitor,
			'id' => $visitor->getId(),
			'ticket' => $ticket));
        } 
    	catch(\Stripe\Error\Card $e) 
    	{

            $this->addFlash("error","Snif ça marche pas :(");
            return $this->render('DAOTicketingBundle:Payment:base.html.twig', array(
			'visitor' => $visitor,
			'id' => $visitor->getId()));
            // The card has been declined
		}
	}

	public function paymentConfirmingAction(Request $request)
	{
		return $this->render('DAOTicketingBundle:Payment:confirming.html.twig');
	}
}