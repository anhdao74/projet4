<?php
namespace DAO\TicketingBundle\Mailer;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use DAO\TicketingBundle\Entity\Ticket;
use DAO\TicketingBundle\Entity\Visitor;

class Mailer
{
    /**
    * @var \Swift_Mailer
    */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Envoi d'un email
     *
     * @param $to
     * @param $body
     */
    public function sendMail($to, $body)
    {
        $mail = \Swift_Message::newInstance();
        $mail
            ->setFrom(array('anhdao.lelievre@gmail.com' => 'LouvreConfirmation'))
            ->setTo($to)
            ->setSubject('Vos billets Visite du Louvre')
            ->setBody($body)
            ->setContentType('text/html')
        ;
        $this->mailer->send($mail);
    }
    /**
     * Construction d'un email pour l'envoi des tickets
     *
     * @param TicketOrder $order
     */
    public function sendTicket($visitors, Ticket $ticket)
    {
        $to         = $ticket->getMailVisiteur();
        $body       = $this->templating->render('DAOTicketingBundle:Mail:ticket.html.twig', array(
            'visitors' => $visitors,
            'ticket' => $ticket));
        $this->sendMail($to, $body);
    }
}