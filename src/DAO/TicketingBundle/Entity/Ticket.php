<?php

namespace DAO\TicketingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use DAO\TicketingBundle\Validator\Constraints as TicketAssert;

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
     * @var \Date
     *
     * @ORM\Column(name="date_resa", type="date")
     * @Assert\Date()
     * @TicketAssert\Verify
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
    * @var string
    *
    * @ORM\Column(name="resaCode", type="string", length=255, unique=true)
    */
    private $resaCode;

    /**
    *@ORM\Column(name="nb_tickets", type="integer")
    * @Assert\Range(min=1, max=100)
    */
    private $nbTickets;

    /**
    * @var int
    *
    * @ORM\OneToMany(targetEntity="DAO\TicketingBundle\Entity\Visitor", mappedBy="ticket", cascade={"remove"})
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
        $this->mailVisiteur = strtolower($mailVisiteur);

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

    public function __toString()
    {
        return $this->getDateResa();
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

    /**
     * Set resaCode.
     *
     * @param string $resaCode
     *
     * @return Ticket
     */
    public function setResaCode($resaCode)
    {
        $this->resaCode = $resaCode;

        return $this;
    }

    /**
     * Get resaCode.
     *
     * @return string
     */
    public function getResaCode()
    {
        return $this->resaCode;
    }

    /**
     * Calcule le montant total de la commande
     *
     * @return int
     */
    /*public function getTotalPrice()
    {
        $visitors = $this->getNbTickets();
        $total = 0;
        foreach($visitors as $visitor){
            $total = $total + $visitor->getPrix();
        }
        return $total;
    }*/
}
