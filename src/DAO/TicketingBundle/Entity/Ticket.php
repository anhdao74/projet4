<?php

namespace DAO\TicketingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="DAO\TicketingBundle\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_resa", type="date")
     * @Assert\Date()
     */
    private $dateResa;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_visiteur", type="string", length=255)
     * @Assert\Email
     */
    private $mailVisiteur;

    /**
    *@ORM\Column(name="ticket_type", type="string", columnDefinition="ENUM('Journée', 'Demi-journée')")
    */
    private $ticketType;

    /**
    *@ORM\Column(name="nb_tickets", type="integer")
    * @Assert\Range(min=1, max=100)
    */
    private $nbTickets;

    /**
    * @ORM\ManyToMany(targetEntity="DAO\TicketingBundle\Entity\Visitor", cascade={"persist"})
    * @ORM\JoinTable(name="dao_ticket_visitor")
    * @Assert\Valid()
    
    */
    private $visitors;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateResa
     *
     * @param \Date $dateResa
     *
     * @return Ticket
     */
    public function setDateResa($dateResa)
    {
        $this->dateResa = $dateResa;

        return $this;
    }

    /**
     * Get dateResa
     *
     * @return \Date
     */
    public function getDateResa()
    {
        return $this->dateResa;
    }

    /**
     * Set ticketType.
     *
     * @param string $ticketType
     *
     * @return Ticket
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType.
     *
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Set nbTickets.
     *
     * @param integer $nbTickets
     *
     * @return Ticket
     */
    public function setNbTickets($nbTickets)
    {
        $this->nbTickets = $nbTickets;

        return $this;
    }

    /**
     * Get nbTickets.
     *
     * @return integer
     */
    public function getNbTickets()
    {
        return $this->nbTickets;
    }

    /**
     * Set mailVisiteur.
     *
     * @param string $mailVisiteur
     *
     * @return Ticket
     */
    public function setMailVisiteur($mailVisiteur)
    {
        $this->mailVisiteur = $mailVisiteur;

        return $this;
    }

    /**
     * Get mailVisiteur.
     *
     * @return string
     */
    public function getMailVisiteur()
    {
        return $this->mailVisiteur;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visitors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add visitor.
     *
     * @param \DAO\TicketingBundle\Entity\Visitor $visitor
     *
     * @return Ticket
     */
    public function addVisitor(\DAO\TicketingBundle\Entity\Visitor $visitor)
    {
        $this->visitors[] = $visitor;

        // On lie l'annonce à la candidature
        $visitor->setTicket($this);

        return $this;
    }

    /**
     * Remove visitor.
     *
     * @param \DAO\TicketingBundle\Entity\Visitor $visitor
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVisitor(\DAO\TicketingBundle\Entity\Visitor $visitor)
    {
        return $this->visitors->removeElement($visitor);
    }

    /**
     * Get visitors.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}
