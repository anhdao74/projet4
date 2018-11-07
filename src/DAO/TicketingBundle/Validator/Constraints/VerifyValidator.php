<?php

namespace DAO\TicketingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use DAO\TicketingBundle\Service\TicketService;

class VerifyValidator extends ConstraintValidator
{    
    public function validate($date_valide, Constraint $constraint) {

        $date = new \DateTime('now');
        $annee = $date_valide->format('Y');
        $paques = date('Y/m/d',TicketService::paques(1, $annee));

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
                    $this->context->addViolation($constraint->message1);
            } 
        }

        foreach($tabDatesPaques as $key => $tabDatePaques){
            //var_dump($tabDate);
            if (strtotime($date_valide->format('Y-m-d')) === strtotime($tabDatePaques)){
                    $this->context->addViolation($constraint->message1);
            } 
        }
        if ($date_valide->format('D') == "Tue") {
            $this->context->addViolation($constraint->message1);
        }elseif ($date_valide->format('D') == "Sun") {
                $this->context->addViolation($constraint->message1);
        }elseif (strtotime($date_valide->format('Y/m/d')) < strtotime($date->format('Y/m/d'))) {
                $this->context->addViolation($constraint->message2);
        }elseif (strtotime($date_valide->format('Y/m/d')) === strtotime($date->format('Y/m/d')) && ($date->format('H') >= 14 &&  ($type_valide == true))) {
                $this->context->addViolation($constraint->message3);
        }
    }
}
