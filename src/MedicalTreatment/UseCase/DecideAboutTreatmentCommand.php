<?php

declare(strict_types=1);

namespace App\MedicalTreatment\UseCase;

class DecideAboutTreatmentCommand
{
    public function __construct(public readonly string $token, public readonly string $userId) {}
}
