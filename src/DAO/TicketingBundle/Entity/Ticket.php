<?php

namespace DAO\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var string
     *
     * @ORM\Column(name="tarif", type="string", length=255)
     */
    private $tarif;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_resa", type="datetimetz")
     */
    private $dateResa;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_visiteur", type="string", length=255)
     */
    private $nomVisiteur;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom_visiteur", type="string", length=255)
     */
    private $prenomVisiteur;

    /**
     * @var date
     *
     * @ORM\Column(name="birth_visiteur", type="date", length=255)
     */
    private $birthVisiteur;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="code_resa", type="string", length=255)
     */
    private $codeResa;

    /**
    *@ORM\Column(name="reduced", type="boolean")
    */
    private $reduced = false;

    /**
    *@ORM\Column(name="ticket_type", type="boolean")
    */
    private $ticketType;

    /**
    *@ORM\Column(name="nb_tickets", type="string")
    */
    private $nbTickets;

    public function __construct()
    {
        $this->date = new \Datetime();
    }

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
     * Set tarif
     *
     * @param string $tarif
     *
     * @return Ticket
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return string
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set dateResa
     *
     * @param \DateTime $dateResa
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
     * @return \DateTime
     */
    public function getDateResa()
    {
        return $this->dateResa;
    }

    /**
     * Set nomVisiteur
     *
     * @param string $nomVisiteur
     *
     * @return Ticket
     */
    public function setNomVisiteur($nomVisiteur)
    {
        $this->nomVisiteur = $nomVisiteur;

        return $this;
    }

    /**
     * Get nomVisiteur
     *
     * @return string
     */
    public function getNomVisiteur()
    {
        return $this->nomVisiteur;
    }

    /**
     * Set codeResa
     *
     * @param string $codeResa
     *
     * @return Ticket
     */
    public function setCodeResa($codeResa)
    {
        $this->codeResa = $codeResa;

        return $this;
    }

    /**
     * Get codeResa
     *
     * @return string
     */
    public function getCodeResa()
    {
        return $this->codeResa;
    }

    /**
     * Set reduced
     *
     * @param \boulean $reduced
     *
     * @return Ticket
     */
    public function setReduced(\boulean $reduced)
    {
        $this->reduced = $reduced;

        return $this;
    }

    /**
     * Get reduced
     *
     * @return \boulean
     */
    public function getReduced()
    {
        return $this->reduced;
    }

    /**
     * Set ticketType.
     *
     * @param bool $ticketType
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
     * @return bool
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Set nbTickets.
     *
     * @param string $nbTickets
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
     * @return string
     */
    public function getNbTickets()
    {
        return $this->nbTickets;
    }

    /**
     * Set prenomVisiteur.
     *
     * @param string $prenomVisiteur
     *
     * @return Ticket
     */
    public function setPrenomVisiteur($prenomVisiteur)
    {
        $this->prenomVisiteur = $prenomVisiteur;

        return $this;
    }

    /**
     * Get prenomVisiteur.
     *
     * @return string
     */
    public function getPrenomVisiteur()
    {
        return $this->prenomVisiteur;
    }

    /**
     * Set pays.
     *
     * @param string $pays
     *
     * @return Ticket
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays.
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set birthVisiteur.
     *
     * @param \DateTime $birthVisiteur
     *
     * @return Ticket
     */
    public function setBirthVisiteur($birthVisiteur)
    {
        $this->birthVisiteur = $birthVisiteur;

        return $this;
    }

    /**
     * Get birthVisiteur.
     *
     * @return \DateTime
     */
    public function getBirthVisiteur()
    {
        return $this->birthVisiteur;
    }
}
