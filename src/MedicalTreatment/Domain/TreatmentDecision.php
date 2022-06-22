<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain;

enum TreatmentDecision: string
{
    case YES = 'yes';
    case NO = 'no';
}
