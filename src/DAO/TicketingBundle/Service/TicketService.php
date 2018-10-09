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

	public function paques($Jourj=0, $annee=NULL)
	{
	    $annee=($annee==NULL) ? date("Y") : $annee;

	    $G = $annee%19;
	    $C = floor($annee/100);
	    $C_4 = floor($C/4);
	    $E = floor((8*$C + 13)/25);
	    $H = (19*$G + $C - $C_4 - $E + 15)%30;

	    if($H==29)
	    {
	        $H=28;
	    }
	    elseif($H==28 && $G>10)
	    {
	        $H=27;
	    }
	    $K = floor($H/28);
	    $P = floor(29/($H+1));
	    $Q = floor((21-$G)/11);
	    $I = ($K*$P*$Q - 1)*$K + $H;
	    $B = floor($annee/4) + $annee;
	    $J1 = $B + $I + 2 + $C_4 - $C;
	    $J2 = $J1%7; //jour de pâques (0=dimanche, 1=lundi....)
	    $R = 28 + $I - $J2; // résultat final :)
	    $mois = $R>30 ? 4 : 3; // mois (1 = janvier, ... 3 = mars...)
	    $Jour = $mois==3 ? $R : $R-31;

	    return mktime(0,0,0,$mois,$Jour+$Jourj,$annee);
	}

	public function isValideDate($date_valide, $nbTickets, $ticketCount, $type_valide)
	{
		$date = new \DateTime('now');

		$date1 = new \DateTime("05/01");
		$date2 = new \DateTime("11/01");
		$date3 = new \DateTime("12/25");
		$date4 = new \DateTime("01/01");
		$date5 = new \DateTime("05/08");
		$date6 = new \DateTime("07/14");
		$date7 = new \DateTime("08/15");
		$date8 = new \DateTime("11/11");
		$annee = $date_valide->format('Y');
		$paques = date('Y/m/d',self::paques(1, $annee));
		$date10 = date('Y-m-d', strtotime($paques.' +38 days'));
		$date11 = date('Y-m-d', strtotime($paques.' +49 days'));

		$capacityCheck = self::capacityCheck($ticketCount, $nbTickets);
		
		$isValideDate = '';

		if (strtotime($date_valide->format('m/d')) === strtotime($date1->format('m/d'))	){
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date2->format('m/d')) ) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date3->format('m/d'))) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date4->format('m/d'))) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date5->format('m/d'))) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date6->format('m/d'))) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date7->format('m/d'))) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('m/d')) === strtotime($date8->format('m/d'))) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($paques)) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date10)) {
				$isValideDate = 1;
			}elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date11)) {
				$isValideDate = 1;
			}elseif ($date_valide->format('D') == "Tue") {
				$isValideDate = 1;
			}elseif ($date_valide->format('D') == "Sun") {
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