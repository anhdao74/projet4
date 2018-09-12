<?php

namespace Tests\TickectingBundle\TicketControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DAO\TicketingBundle\Controller\TicketController;

class TicketControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/home');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Bienvenue sur le site de la billeterie en ligne', $crawler->filter('.well4 p')->text());
    }

    public function TestDeleteTicket()
    {
        $ticketDay = new Ticket(300, 2018/12/27, jean829@msn.com, 1, V20181227Z1louvre, 1);
        //$ticketDay->getId(300);
        $crawler = $client->request('DELETE', '/deleteTicket/'.$ticketDay->getId()); 
        
    }
}