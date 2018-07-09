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
		$ticket->setDateResa(new \DateTime());

		$form = $this->get('form.factory')->create(TicketType::class, $ticket);
		$form->handleRequest($request);

		
		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$code1 = $ticket->getId();
			$code2 = $ticket->getDateResa();
			
			$ticket->setResaCode('louvre1') ;
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
		$req = $request->request->all();
		//var_dump($req);		
    		$visitor = new Visitor();			
    		
			$form = $this->get('form.factory')->create(VisitorType::class, $visitor);

				if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
				{
					$visitor->setTicket($ticket);
					//$groupes = $form['$i']->getData()["groupes"];
					$age = $visitor->getAge();

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
					$visitor->setPrix($priceType);

					$em = $this->getDoctrine()->getManager();	
					//foreach ($groupes as $groupe) {
					$em->persist($visitor);//}
					$em->flush();

					/*incrementer index statique*/
					for ($i = $nbTickets; $i<1; $i--)
					{
					var_dump($i);
						if ($i > 0)
						{
							return $this->render('DAOTicketingBundle:Ticket:register.html.twig', array( 
								'form' => $form->createView(),
								'ticket' => $ticket,
								));
							$nbTickets--;
							break;
						}
					}
					//return $this->registerSummeryAction($request);
					return $this->redirectToRoute('dao_ticketing_summery', array(
								'id' => $visitor->getId()));					
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
	//var_dump($visitor);
	$req = $request->request->all();

		//$content = $this->get('templating')->render('DAOTicketingBundle:Ticket:recapitulatif.html.twig');
		
		return $this->render('DAOTicketingBundle:Ticket:recapitulatif.html.twig', array(
			'visitor' => $visitor));
	}
}