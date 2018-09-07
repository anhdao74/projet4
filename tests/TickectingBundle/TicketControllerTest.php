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
}