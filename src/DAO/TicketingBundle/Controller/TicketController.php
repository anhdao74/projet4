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

	public function capacityCheckAction(\DateTime $date, $tickets, Request $request)
	{
		$ticketCount = $this->em->getRepository('DAOTicketingBundle:Ticket')->getTicketsCount($date);

        return ( ($ticketCount + $tickets) > 1000 ) ? false : true;
	}

	public function remainingTicketAction(\DateTime $date, $tickets, Request $request)
	{
		$maxTicket = 1000;
		$ticketCount = $this->em->getRepository('DAOTicketingBundle:Ticket')->getTicketsCount($date);

        return (int) ($this-> $maxTicket - $ticketCount);
	}

	public function checkDateAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id); 

		$date1 = new \DateTime("05/01/2018");
		$date2 = new \DateTime("11/01/2018");
		$date3 = new \DateTime("12/25/2018");

		$date_valide = $ticket->getDateResa();
		
		if (strtotime($date_valide->format('Y/m/d')) !== strtotime($date1->format('Y/m/d'))
			|| strtotime($date_valide->format('Y/m/d')) !== strtotime($date2->format('Y/m/d')) 
			|| strtotime($date_valide->format('Y/m/d')) !== strtotime($date3->format('Y/m/d'))){
				return true;
		}
	}

	public function registerDateAction( Request $request)
	{
		
		$ticket = new Ticket;
		
		$date1 = new \DateTime("05/01/2018");
		$date2 = new \DateTime("11/01/2018");
		$date3 = new \DateTime("12/25/2018");

		$form = $this->get('form.factory')->create(TicketType::class, $ticket);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			
			$ticket->setResaCode('louvre6') ;

			$date_valide = $ticket->getDateResa();

			//$date = self::checkDateAction($request);

			if (strtotime($date_valide->format('Y/m/d')) === strtotime($date1->format('Y/m/d'))	){
				echo "Nous fermons nos portes le 1er mai, le 1er novembre, le 25 décembre et tous les mardi";
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
			}elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date2->format('Y/m/d')) ) {
				echo "Nous fermons nos portes le 1er mai, le 1er novembre, le 25 décembre et tous les mardi";
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
			}elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date3->format('Y/m/d'))) {
				echo "Nous fermons nos portes le 1er mai, le 1er novembre, le 25 décembre et tous les mardi";
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
			}elseif (strtotime($date_valide->format('D')) == "Tue") {
				echo "Nous fermons nos portes le 1er mai, le 1er novembre, le 25 décembre et tous les mardi";
				return $this->render('DAOTicketingBundle:Ticket:dating.html.twig', array('form' => $form->createView()));
			}else{
				//var_dump($date_valide->format('D'));

				//exit();
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

	public function registerVisitorAction ($id, $nbTickets, $current, Request $request)
	{	
		$em = $this->getDoctrine()->getManager();
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);
		//var_dump($ticket);
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
					
					if ($current < $nbTickets)
					{
						$current++;
						var_dump($current);
						return $this->redirectToRoute('dao_ticketing_register', array( 
							'id' => $ticket->getId(),
							'nbTickets' => $ticket->getNbTickets(),
							'current' => $current,
							'ticket' => $ticket

							));
					}

					return $this->redirectToRoute('dao_ticketing_summery', array(
								'id' => $ticket->getId(),
								//'visitors' => $visitor->getTicket(),
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
    	$ticket = $em->getRepository('DAOTicketingBundle:Ticket')->find($id);

	$em = $this->getDoctrine()->getManager();
    $visitor = $em->getRepository('DAOTicketingBundle:Visitor')->find($id);

	$visitors = $this->getDoctrine()
      ->getManager()
      ->getRepository('DAOTicketingBundle:Visitor')
      ->findBy(array('ticket' => $ticket))
    ;
    //var_dump($visitors);

		return $this->render('DAOTicketingBundle:Ticket:recapitulatif.html.twig', array(
			'visitor' => $visitor,
			'visitors' => $visitors,
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