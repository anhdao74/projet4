<?php

namespace DAO\TicketingBundle\Service;

use DAO\TicketingBundle\Entity\Ticket;

class TicketService
{
	public function sumTotal($prix)
	{	
	    $total = 0;

		foreach ($prix as $pri) {
			$total += $pri;
		}
    	
    	return $total;	
	}

	public function isValideDate($date_valide, $nbTickets, $ticketCount, $type_valide)
	{
		$date = new \DateTime('now');

		$date1 = new \DateTime("05/01");
		$date2 = new \DateTime("11/01");
		$date3 = new \DateTime("12/25");

		$capacityCheck = self::capacityCheck($ticketCount, $nbTickets);
		
		$isValideDate = '';

		if (strtotime($date_valide->format('m/d')) === strtotime($date1->format('m/d'))	){
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date2->format('m/d')) ) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date3->format('m/d'))) {
				$isValideDate = 1;
			}elseif ($date_valide->format('D') == "Tue") {
				$isValideDate = 1;
			}elseif ($capacityCheck == false) {
				$isValideDate = 5;	
			}elseif (strtotime($date_valide->format('Y/m/d')) < strtotime($date->format('Y/m/d'))) {
				$isValideDate = 3;
			}elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date->format('Y/m/d')) && ($date->format('H') > 14 &&  ($type_valide == true))) {
				$isValideDate = 4;	
		}else{
			$isValideDate = 2;
		}
		
		return $isValideDate;
	}

	public function capacityCheck($ticketCount, $nbTickets)
	{
        return ( ($ticketCount + $nbTickets) > 1000 ) ? false : true;
	}

	public function getPrice($age, $reduced){
		if($age < 4) {
				$priceType = '0';
			}elseif($age >= 4 && $age < 12) {
				$priceType = '8';
			}elseif ($age >=60) {
				$priceType = '12';
			}else {
				$priceType = '16';
			}

			if($reduced === true) {
				$priceType = ($priceType-10);
			}
		return $priceType;
	}
}