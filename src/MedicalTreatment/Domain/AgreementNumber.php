<?php

declare(strict_types=1);

namespace App\MedicalTreatment\Domain;

final class AgreementNumber
{
    public function __construct(private readonly string $number) {}

    public function toString(): string
    {
        return $this->number;
    }
}
