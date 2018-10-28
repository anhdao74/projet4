<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use DAO\TicketingBundle\Service\TicketService;

/**
 * @Annotation
 */
class ValideDateValidator extends ConstraintValidator
{
    public function ValidateDate($date_valide) {
        $date = new \DateTime('now');
        $annee = $date_valide->format('Y');
        $paques = date('Y/m/d',self::paques(1, $annee));

        $tabDates = array(
            1 => new \DateTime("05/01"),
            2 => new \DateTime("11/01"),
            3 => new \DateTime("12/25"),
            4 => new \DateTime("01/01"),
            5 => new \DateTime("05/08"),
            6 => new \DateTime("07/14"),
            7 => new \DateTime("08/15"),
            8 => new \DateTime("11/11"),
        );

        $tabDatesPaques = array(
            1 => $paques,
            2 => date('Y-m-d', strtotime($paques.' +38 days')),
            3 => date('Y-m-d', strtotime($paques.' +49 days')),
        );
        
        $isValideDate = '';
        foreach($tabDates as $key => $tabDate){
            //var_dump($tabDate);
            if (strtotime($date_valide->format('m/d')) === strtotime($tabDate->format('m/d'))){
                    $isValideDate = 1;
                    return $isValideDate;
            } 
        }

        foreach($tabDatesPaques as $key => $tabDatePaques){
            //var_dump($tabDate);
            if (strtotime($date_valide->format('Y-m-d')) === strtotime($tabDatePaques)){
                    $isValideDate = 1;
                    return $isValideDate;
            } 
        }
        if ($date_valide->format('D') == "Tue") {
            $isValideDate = 1;
        }elseif ($date_valide->format('D') == "Sun") {
                $isValideDate = 1;
        }elseif (strtotime($date_valide->format('Y/m/d')) < strtotime($date->format('Y/m/d'))) {
                $isValideDate = 3;
        }elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date->format('Y/m/d')) && ($date->format('H') >= 14 &&  ($type_valide == true))) {
                $isValideDate = 4;	
        }else{
                $isValideDate = 2;
        }
        return $isValideDate;
    }
}
