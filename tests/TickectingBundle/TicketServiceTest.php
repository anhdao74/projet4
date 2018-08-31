<?php

namespace Tests\TickectingBundle\TicketServiceTest;

use PHPUnit\Framework\TestCase;
use DAO\TicketingBundle\Service\TicketService;

class TicketServiceTest extends TestCase
{
	public function testSumTotal()
    {
        $ticketService = new TicketService();
        $result = $ticketService->sumTotal([16, 12]);

        $this->assertEquals(28, $result);
    }

    public function testDate()
    {
    	$ticketService = new TicketService();
    	$result = $ticketService->isValideDate(new \DateTime("12/25"),2 ,1000);

    	$this->assertEquals(1, $result);
    }

    public function testCapacity()
    {
        $ticketService = new TicketService();
        $result = $ticketService->capacityCheck(1000, 1);

        $this->assertEquals(false, $result);
    }

}