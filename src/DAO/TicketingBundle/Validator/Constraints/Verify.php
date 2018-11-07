<?php

// src/DAO/TicketingBundle/Validator/Constraints/Verify.php

namespace DAO\TicketingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Verify extends Constraint
{
    public $message1 = 'Nous fermons nos portes tous les mardi, les dimanches et jours fériés';
    public $message2 = 'Vous ne pouvez pas réserver pour les jours passés';
    public $message3 = "Vous ne pouvez pas réserver un ticket journée pour aujourd'hui, vous pouvez choisir un billet demi-journéé ou un autre jour.";
    public $message4 = "Un enfant ayant moins de 4 doit être accompagné d'un adulte";
}